<?php

use App\Models\Category;
use App\Models\Operation;
use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('categories page loads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.categories.index'));

    $response->assertStatus(200);
});

test('create category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.categories.store'), [
        'name' => 'Test Category',
        'type' => 'expense',
        'color' => '#FF5733',
        'icon' => 'fas fa-home',
    ]);

    $response->assertRedirect(route('dashboard.categories.index'));
    $response->assertSessionHas('success', 'Categoría creada correctamente');

    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'type' => 'expense',
        'user_id' => $user->id,
    ]);
});

test('validation errors on create category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.categories.store'), []);

    $response->assertSessionHasErrors(['name', 'type', 'color', 'icon']);
});

test('validation errors on update category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('dashboard.categories.update', $category), [
        'name' => '',
        'type' => 'invalid',
    ]);

    $response->assertSessionHasErrors(['name', 'type']);
});

test('icon must be in allowlist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.categories.store'), [
        'name' => 'Invalid Icon Category',
        'type' => 'expense',
        'color' => '#112233',
        'icon' => 'fas fa-not-a-real-icon',
    ]);

    $response->assertSessionHasErrors(['icon']);
});

test('color must be a hex value', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.categories.store'), [
        'name' => 'Invalid Color Category',
        'type' => 'expense',
        'color' => 'red',
        'icon' => 'fas fa-home',
    ]);

    $response->assertSessionHasErrors(['color']);
});

test('user cannot update another user category', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->put(route('dashboard.categories.update', $category), [
        'name' => 'Hacked Name',
        'type' => 'income',
        'color' => '#000000',
        'icon' => 'fas fa-home',
    ]);

    $response->assertStatus(403);
});

test('user cannot delete another user category', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->delete(route('dashboard.categories.destroy', $category));

    $response->assertStatus(403);
});

test('categories are filtered by user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userCategory = Category::factory()->create(['user_id' => $user->id, 'name' => 'User Category']);
    Category::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other User Category']);

    $response = $this->actingAs($user)->get(route('dashboard.categories.index'));

    $response->assertViewHas('categories', function ($categories) {
        $names = collect($categories->items())->pluck('name')->all();

        return in_array('User Category', $names, true)
            && ! in_array('Other User Category', $names, true);
    });
});

test('search filters categories by name', function () {
    $user = User::factory()->create();
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Food']);
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Other Unique Category Name']);

    $response = $this->actingAs($user)->get(route('dashboard.categories.index', ['search' => 'Food']));

    $response->assertViewHas('categories', function ($categories) {
        $names = collect($categories->items())->pluck('name')->all();

        return in_array('Food', $names, true)
            && ! in_array('Other Unique Category Name', $names, true);
    });
});

test('ajax create category returns json', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('dashboard.categories.store'), [
        'name' => 'Ajax Category',
        'type' => 'income',
        'color' => '#112233',
        'icon' => 'fas fa-home',
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('message', 'Categoría creada correctamente')
        ->assertJsonStructure(['data' => ['id']]);
});

test('ajax update category returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

    $response = $this->actingAs($user)->putJson(route('dashboard.categories.update', $category), [
        'name' => 'Updated via Ajax',
        'type' => 'expense',
        'color' => '#445566',
        'icon' => 'fas fa-car',
    ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('message', 'Categoría actualizada correctamente')
        ->assertJsonStructure(['data' => ['id']]);
});

test('ajax delete category returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson(route('dashboard.categories.destroy', $category));

    $response
        ->assertSuccessful()
        ->assertJsonPath('message', 'Categoría eliminada correctamente');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('cannot delete category when it is in use', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $user->id]);
    Operation::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
        'type' => 'expense',
    ]);

    $response = $this->actingAs($user)->deleteJson(route('dashboard.categories.destroy', $category));

    $response
        ->assertStatus(409)
        ->assertJsonPath('message', 'No se puede eliminar: la categoría está en uso.');
});

test('cannot change category type when it is in use', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
    $account = Account::factory()->create(['user_id' => $user->id]);
    Operation::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
        'type' => 'income',
    ]);

    $response = $this->actingAs($user)->putJson(route('dashboard.categories.update', $category), [
        'name' => 'Type change attempt',
        'type' => 'expense',
        'color' => '#112233',
        'icon' => 'fas fa-home',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});
