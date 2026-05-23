<?php

use App\Models\Category;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('goals page loads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.goals.index'));

    $response->assertStatus(200);
});

test('create goal', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('dashboard.goals.store'), [
        'name' => 'Test Goal',
        'target_amount' => 1000,
        'due_date' => '2025-12-31',
        'category_id' => $category->id,
    ]);

    $response->assertRedirect(route('dashboard.goals.index'));
    $response->assertSessionHas('success', 'Meta creada correctamente');

    $this->assertDatabaseHas('goals', [
        'name' => 'Test Goal',
        'target_amount' => 1000,
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);
});

test('update goal', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'name' => 'Old Goal',
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.goals.update', $goal), [
        'name' => 'Updated Goal',
        'target_amount' => 2000,
        'due_date' => '2026-12-31',
        'category_id' => $category->id,
    ]);

    $response->assertRedirect(route('dashboard.goals.index'));
    $response->assertSessionHas('success', 'Meta actualizada correctamente');

    $this->assertDatabaseHas('goals', [
        'id' => $goal->id,
        'name' => 'Updated Goal',
        'target_amount' => 2000,
    ]);
});

test('delete goal', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->delete(route('dashboard.goals.destroy', $goal));

    $response->assertRedirect(route('dashboard.goals.index'));
    $response->assertSessionHas('success', 'Meta eliminada correctamente');

    $this->assertDatabaseMissing('goals', [
        'id' => $goal->id,
    ]);
});

test('add payment to goal', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'target_amount' => 1000,
        'current_amount' => 0,
    ]);

    $response = $this->actingAs($user)->post(route('dashboard.goals.payment', $goal), [
        'payment_amount' => 500,
    ]);

    $response->assertRedirect(route('dashboard.goals.index'));
    $response->assertSessionHas('success', 'Pago añadido correctamente');

    $this->assertDatabaseHas('goals', [
        'id' => $goal->id,
        'current_amount' => 500,
    ]);
});

test('mark goal as completed', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'target_amount' => 1000,
        'current_amount' => 1000,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->post(route('dashboard.goals.complete', $goal));

    expect($response->status())->toBe(302);
    expect($goal->fresh()->status)->toBe('completed');
});

test('validation errors', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.goals.store'), []);

    $response->assertSessionHasErrors(['name', 'target_amount', 'due_date', 'category_id']);
});

test('goals are filtered by user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $userGoal = Goal::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
    Goal::factory()->create(['user_id' => $otherUser->id, 'category_id' => $category->id]);

    $response = $this->actingAs($user)->get(route('dashboard.goals.index'));

    $response->assertViewHas('goals', function ($goals) use ($userGoal) {
        return $goals->contains($userGoal);
    });
});

test('create goal via ajax returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('dashboard.goals.store'), [
        'name' => 'Test Goal',
        'target_amount' => 1000,
        'due_date' => '2025-12-31',
        'category_id' => $category->id,
    ]);

    $response->assertCreated()->assertJson([
        'message' => 'Meta creada correctamente',
    ]);
});

test('update goal via ajax returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'name' => 'Old Goal',
    ]);

    $response = $this->actingAs($user)->putJson(route('dashboard.goals.update', $goal), [
        'name' => 'Updated Goal',
        'target_amount' => 2000,
        'due_date' => '2026-12-31',
        'category_id' => $category->id,
    ]);

    $response->assertOk()->assertJson([
        'message' => 'Meta actualizada correctamente',
    ]);
});

test('delete goal via ajax returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->deleteJson(route('dashboard.goals.destroy', $goal));

    $response->assertOk()->assertJson([
        'message' => 'Meta eliminada correctamente',
    ]);
});

test('add payment to goal via ajax returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'target_amount' => 1000,
        'current_amount' => 0,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->postJson(route('dashboard.goals.payment', $goal), [
        'payment_amount' => 500,
    ]);

    $response->assertOk()->assertJson([
        'message' => 'Pago añadido correctamente',
    ]);

    $this->assertDatabaseHas('goals', [
        'id' => $goal->id,
        'current_amount' => 500,
    ]);
});

test('goal payment cannot exceed remaining amount (ajax)', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $goal = Goal::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'target_amount' => 100,
        'current_amount' => 0,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->postJson(route('dashboard.goals.payment', $goal), [
        'payment_amount' => 200,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['payment_amount']);
});

test('cannot add payment to other user goal', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id]);
    $goal = Goal::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'target_amount' => 100,
        'current_amount' => 0,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->postJson(route('dashboard.goals.payment', $goal), [
        'payment_amount' => 10,
    ]);

    $response->assertForbidden();
});
