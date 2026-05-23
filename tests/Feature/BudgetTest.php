<?php

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('budgets page loads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.budgets.index'));

    $response->assertStatus(200);
});

test('create budget with multiple categories', function () {
    $user = User::factory()->create();
    $categoryA = Category::factory()->expense()->create(['user_id' => $user->id]);
    $categoryB = Category::factory()->expense()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('dashboard.budgets.store'), [
        'name' => 'Presupuesto Comida',
        'period' => 'monthly',
        'limit_amount' => 500.00,
        'categories' => [$categoryA->id, $categoryB->id],
    ]);

    $response->assertRedirect(route('dashboard.budgets.index'));
    $response->assertSessionHas('success', 'Presupuesto creado correctamente');

    $budget = Budget::query()->where('user_id', $user->id)->where('name', 'Presupuesto Comida')->firstOrFail();

    $this->assertDatabaseHas('budgets', [
        'id' => $budget->id,
        'user_id' => $user->id,
        'period' => 'monthly',
    ]);

    $this->assertDatabaseHas('budget_category', [
        'budget_id' => $budget->id,
        'category_id' => $categoryA->id,
    ]);
    $this->assertDatabaseHas('budget_category', [
        'budget_id' => $budget->id,
        'category_id' => $categoryB->id,
    ]);
});

test('create budget rejects non-expense categories', function () {
    $user = User::factory()->create();
    $incomeCategory = Category::factory()->income()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('dashboard.budgets.store'), [
        'name' => 'Presupuesto inválido',
        'period' => 'monthly',
        'limit_amount' => 100.00,
        'categories' => [$incomeCategory->id],
    ]);

    $response->assertSessionHasErrors(['categories.0']);
});

test('update budget syncs categories', function () {
    $user = User::factory()->create();
    $categoryA = Category::factory()->expense()->create(['user_id' => $user->id]);
    $categoryB = Category::factory()->expense()->create(['user_id' => $user->id]);
    $categoryC = Category::factory()->expense()->create(['user_id' => $user->id]);

    $budget = Budget::factory()->create([
        'user_id' => $user->id,
        'name' => 'Viejo presupuesto',
        'period' => 'monthly',
        'limit_amount' => 100.00,
    ]);
    $budget->categories()->sync([$categoryA->id, $categoryB->id]);

    $response = $this->actingAs($user)->put(route('dashboard.budgets.update', $budget), [
        'name' => 'Nuevo presupuesto',
        'period' => 'weekly',
        'limit_amount' => 250.00,
        'categories' => [$categoryC->id],
    ]);

    $response->assertRedirect(route('dashboard.budgets.index'));
    $response->assertSessionHas('success', 'Presupuesto actualizado correctamente');

    $this->assertDatabaseHas('budgets', [
        'id' => $budget->id,
        'name' => 'Nuevo presupuesto',
        'period' => 'weekly',
    ]);

    $this->assertDatabaseMissing('budget_category', [
        'budget_id' => $budget->id,
        'category_id' => $categoryA->id,
    ]);
    $this->assertDatabaseMissing('budget_category', [
        'budget_id' => $budget->id,
        'category_id' => $categoryB->id,
    ]);
    $this->assertDatabaseHas('budget_category', [
        'budget_id' => $budget->id,
        'category_id' => $categoryC->id,
    ]);
});

test('delete budget removes it', function () {
    $user = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $user->id]);
    $budget = Budget::factory()->create(['user_id' => $user->id]);
    $budget->categories()->sync([$category->id]);

    $response = $this->actingAs($user)->delete(route('dashboard.budgets.destroy', $budget));

    $response->assertRedirect(route('dashboard.budgets.index'));
    $response->assertSessionHas('success', 'Presupuesto eliminado correctamente');

    $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    $this->assertDatabaseMissing('budget_category', ['budget_id' => $budget->id, 'category_id' => $category->id]);
});

test('validation errors on create budget', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.budgets.store'), []);

    $response->assertSessionHasErrors(['name', 'period', 'limit_amount', 'categories']);
});

test('user cannot update another user budget', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $budget = Budget::factory()->create(['user_id' => $otherUser->id]);
    $category = Category::factory()->expense()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('dashboard.budgets.update', $budget), [
        'name' => 'Hacked',
        'period' => 'monthly',
        'limit_amount' => 100,
        'categories' => [$category->id],
    ]);

    $response->assertStatus(403);
});

test('user cannot delete another user budget', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $budget = Budget::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->delete(route('dashboard.budgets.destroy', $budget));

    $response->assertStatus(403);
});

test('budgets are filtered by user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Budget::factory()->create(['user_id' => $user->id, 'name' => 'Mi presupuesto']);
    Budget::factory()->create(['user_id' => $otherUser->id, 'name' => 'Presupuesto ajeno']);

    $response = $this->actingAs($user)->get(route('dashboard.budgets.index'));

    $response->assertViewHas('budgets', function ($budgets) {
        $names = collect($budgets->items())->pluck('name')->all();

        return in_array('Mi presupuesto', $names, true)
            && ! in_array('Presupuesto ajeno', $names, true);
    });
});

test('search filters budgets by name', function () {
    $user = User::factory()->create();
    Budget::factory()->create(['user_id' => $user->id, 'name' => 'Comida mensual']);
    Budget::factory()->create(['user_id' => $user->id, 'name' => 'Transporte']);

    $response = $this->actingAs($user)->get(route('dashboard.budgets.index', ['search' => 'Comida']));

    $response->assertViewHas('budgets', function ($budgets) {
        $names = collect($budgets->items())->pluck('name')->all();

        return in_array('Comida mensual', $names, true)
            && ! in_array('Transporte', $names, true);
    });
});

test('computed spent and status are based on current period operations', function () {
    Carbon::setTestNow(Carbon::parse('2026-05-15 10:00:00'));

    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $categoryInBudget = Category::factory()->expense()->create(['user_id' => $user->id]);
    $otherCategory = Category::factory()->expense()->create(['user_id' => $user->id]);

    $budget = Budget::factory()->create([
        'user_id' => $user->id,
        'name' => 'Presupuesto Mayo',
        'period' => 'monthly',
        'limit_amount' => 100.00,
    ]);
    $budget->categories()->sync([$categoryInBudget->id]);

    Operation::factory()->expense()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $categoryInBudget->id,
        'amount' => 30.00,
        'date_time' => Carbon::parse('2026-05-10 12:00:00'),
    ]);

    Operation::factory()->expense()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $categoryInBudget->id,
        'amount' => 999.00,
        'date_time' => Carbon::parse('2026-04-10 12:00:00'),
    ]);

    Operation::factory()->income()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $categoryInBudget->id,
        'amount' => 200.00,
        'date_time' => Carbon::parse('2026-05-11 12:00:00'),
    ]);

    Operation::factory()->expense()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $otherCategory->id,
        'amount' => 50.00,
        'date_time' => Carbon::parse('2026-05-12 12:00:00'),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.budgets.index'));

    $response->assertViewHas('budgets', function ($budgets) use ($budget) {
        $items = collect($budgets->items());
        $row = $items->firstWhere('id', $budget->id);

        return $row
            && (float) $row->getAttribute('spent_amount') === 30.0
            && $row->getAttribute('status') === 'active';
    });

    Operation::factory()->expense()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $categoryInBudget->id,
        'amount' => 80.00,
        'date_time' => Carbon::parse('2026-05-13 12:00:00'),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.budgets.index', ['status' => 'exceeded']));

    $response->assertViewHas('budgets', function ($budgets) use ($budget) {
        $items = collect($budgets->items());
        $row = $items->firstWhere('id', $budget->id);

        return $row
            && (float) $row->getAttribute('spent_amount') === 110.0
            && $row->getAttribute('status') === 'exceeded';
    });
});

test('create budget via ajax returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('dashboard.budgets.store'), [
        'name' => 'Ajax budget',
        'period' => 'monthly',
        'limit_amount' => 100.00,
        'categories' => [$category->id],
    ]);

    $response->assertCreated()->assertJson([
        'message' => 'Presupuesto creado correctamente',
    ]);
});

test('update budget via ajax returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $user->id]);
    $budget = Budget::factory()->create(['user_id' => $user->id]);
    $budget->categories()->sync([$category->id]);

    $response = $this->actingAs($user)->putJson(route('dashboard.budgets.update', $budget), [
        'name' => 'Updated',
        'period' => 'weekly',
        'limit_amount' => 200.00,
        'categories' => [$category->id],
    ]);

    $response->assertOk()->assertJson([
        'message' => 'Presupuesto actualizado correctamente',
    ]);
});

test('delete budget via ajax returns json', function () {
    $user = User::factory()->create();
    $category = Category::factory()->expense()->create(['user_id' => $user->id]);
    $budget = Budget::factory()->create(['user_id' => $user->id]);
    $budget->categories()->sync([$category->id]);

    $response = $this->actingAs($user)->deleteJson(route('dashboard.budgets.destroy', $budget));

    $response->assertOk()->assertJson([
        'message' => 'Presupuesto eliminado correctamente',
    ]);
});
