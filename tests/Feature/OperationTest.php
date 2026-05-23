<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('operations page loads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.operations.index'));

    $response->assertStatus(200);
});

test('create operation updates account balance for income', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
    $account = Account::factory()->create(['user_id' => $user->id, 'current_balance' => 1000.00]);

    $response = $this->actingAs($user)->post(route('dashboard.operations.store'), [
        'amount' => 100.00,
        'date_time' => now()->format('Y-m-d\TH:i'),
        'type' => 'income',
        'category_id' => $category->id,
        'account_id' => $account->id,
        'note' => 'Test note',
    ]);

    $response->assertRedirect(route('dashboard.operations.index'));
    $response->assertSessionHas('success', 'Operación creada correctamente');

    $account->refresh();
    expect((float) $account->current_balance)->toBe(1100.00);
});

test('create operation updates account balance for expense', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $user->id, 'current_balance' => 1000.00]);

    $response = $this->actingAs($user)->post(route('dashboard.operations.store'), [
        'amount' => 50.00,
        'date_time' => now()->format('Y-m-d\TH:i'),
        'type' => 'expense',
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response->assertRedirect(route('dashboard.operations.index'));

    $account->refresh();
    expect((float) $account->current_balance)->toBe(950.00);
});

test('update operation', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $user->id]);
    $operation = Operation::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
        'amount' => 50.00,
        'type' => 'expense',
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.operations.update', $operation), [
        'amount' => 75.00,
        'date_time' => $operation->date_time->format('Y-m-d\TH:i'),
        'type' => 'expense',
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response->assertRedirect(route('dashboard.operations.index'));
    $response->assertSessionHas('success', 'Operación actualizada correctamente');

    $this->assertDatabaseHas('operations', [
        'id' => $operation->id,
        'amount' => 75.00,
    ]);
});

test('show operation returns json for modal', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $user->id, 'current_balance' => 1000.00]);

    $operation = Operation::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
        'type' => 'expense',
        'amount' => 50.00,
        'date_time' => now()->startOfMinute(),
        'note' => 'Test note',
    ]);

    $response = $this->actingAs($user)->getJson(route('dashboard.operations.show', $operation));

    $response
        ->assertOk()
        ->assertJson([
            'id' => $operation->id,
            'type' => 'expense',
            'amount' => '50.00',
            'note' => 'Test note',
            'category_id' => $category->id,
            'account_id' => $account->id,
            'date_time_input' => $operation->date_time->format('Y-m-d\TH:i'),
        ])
        ->assertJsonPath('category.name', $category->name)
        ->assertJsonPath('account.name', $account->name);
});

test('user cannot view another user operation', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $otherUser->id]);

    $operation = Operation::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
        'type' => 'expense',
        'amount' => 10.00,
    ]);

    $this->actingAs($user)
        ->getJson(route('dashboard.operations.show', $operation))
        ->assertStatus(403);
});

test('moving an expense operation to another account updates both balances correctly', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account1 = Account::factory()->create(['user_id' => $user->id, 'current_balance' => 1000.00]);
    $account2 = Account::factory()->create(['user_id' => $user->id, 'current_balance' => 500.00]);

    $this->actingAs($user)->post(route('dashboard.operations.store'), [
        'amount' => 50.00,
        'date_time' => now()->format('Y-m-d\TH:i'),
        'type' => 'expense',
        'category_id' => $category->id,
        'account_id' => $account1->id,
    ])->assertRedirect(route('dashboard.operations.index'));

    $operation = Operation::query()->firstOrFail();

    $account1->refresh();
    $account2->refresh();
    expect((float) $account1->current_balance)->toBe(950.00);
    expect((float) $account2->current_balance)->toBe(500.00);

    $this->actingAs($user)->put(route('dashboard.operations.update', $operation), [
        'amount' => 30.00,
        'date_time' => now()->format('Y-m-d\TH:i'),
        'type' => 'expense',
        'category_id' => $category->id,
        'account_id' => $account2->id,
    ])->assertRedirect(route('dashboard.operations.index'));

    $account1->refresh();
    $account2->refresh();

    expect((float) $account1->current_balance)->toBe(1000.00);
    expect((float) $account2->current_balance)->toBe(470.00);
});

test('delete operation', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $account = Account::factory()->create(['user_id' => $user->id]);
    $operation = Operation::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response = $this->actingAs($user)->delete(route('dashboard.operations.destroy', $operation));

    $response->assertRedirect(route('dashboard.operations.index'));
    $response->assertSessionHas('success', 'Operación eliminada correctamente');

    $this->assertDatabaseMissing('operations', ['id' => $operation->id]);
});

test('validation errors on create operation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.operations.store'), []);

    $response->assertSessionHasErrors(['amount', 'date_time', 'type', 'category_id', 'account_id']);
});

test('validation errors on update operation', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $account = Account::factory()->create(['user_id' => $user->id]);
    $operation = Operation::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.operations.update', $operation), [
        'amount' => '',
        'type' => 'invalid',
    ]);

    $response->assertSessionHasErrors(['amount', 'type']);
});

test('user cannot update another user operation', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id]);
    $account = Account::factory()->create(['user_id' => $otherUser->id]);
    $operation = Operation::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.operations.update', $operation), [
        'amount' => 999.99,
        'date_time' => now()->format('Y-m-d\TH:i'),
        'type' => 'expense',
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response->assertStatus(403);
});

test('user cannot delete another user operation', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $otherUser->id]);
    $account = Account::factory()->create(['user_id' => $otherUser->id]);
    $operation = Operation::factory()->create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response = $this->actingAs($user)->delete(route('dashboard.operations.destroy', $operation));

    $response->assertStatus(403);
});

test('operations are filtered by user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $account = Account::factory()->create(['user_id' => $user->id]);

    $userOperation = Operation::factory()->create(['user_id' => $user->id, 'category_id' => $category->id, 'account_id' => $account->id]);
    Operation::factory()->create(['user_id' => $otherUser->id, 'category_id' => $category->id, 'account_id' => $account->id]);

    $response = $this->actingAs($user)->get(route('dashboard.operations.index'));

    $response->assertViewHas('operations', function ($operations) use ($userOperation) {
        return $operations->contains($userOperation);
    });
});

test('search filters operations by note', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $account = Account::factory()->create(['user_id' => $user->id]);

    Operation::factory()->create(['user_id' => $user->id, 'category_id' => $category->id, 'account_id' => $account->id, 'note' => 'Unique Search Term']);
    Operation::factory()->create(['user_id' => $user->id, 'category_id' => $category->id, 'account_id' => $account->id, 'note' => 'Other Note']);

    $response = $this->actingAs($user)->get(route('dashboard.operations.index', ['search' => 'Unique']));

    $response->assertViewHas('operations', function ($operations) {
        return $operations->count() === 1;
    });
});

test('filter operations by type', function () {
    $user = User::factory()->create();
    $categoryIncome = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
    $categoryExpense = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $user->id]);

    Operation::factory()->create(['user_id' => $user->id, 'category_id' => $categoryIncome->id, 'account_id' => $account->id, 'type' => 'income']);
    Operation::factory()->create(['user_id' => $user->id, 'category_id' => $categoryExpense->id, 'account_id' => $account->id, 'type' => 'expense']);

    $response = $this->actingAs($user)->get(route('dashboard.operations.index', ['type' => 'income']));

    $response->assertViewHas('operations', function ($operations) {
        return $operations->every(fn($op) => $op->type === 'income');
    });
});

test('filter operations by account', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $account1 = Account::factory()->create(['user_id' => $user->id]);
    $account2 = Account::factory()->create(['user_id' => $user->id]);

    Operation::factory()->create(['user_id' => $user->id, 'category_id' => $category->id, 'account_id' => $account1->id]);
    Operation::factory()->create(['user_id' => $user->id, 'category_id' => $category->id, 'account_id' => $account2->id]);

    $response = $this->actingAs($user)->get(route('dashboard.operations.index', ['account_id' => $account1->id]));

    $response->assertViewHas('operations', function ($operations) use ($account1) {
        return $operations->every(fn($op) => $op->account_id === $account1->id);
    });
});

test('expense operation cannot exceed account balance', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $user->id, 'current_balance' => 100.00]);

    $response = $this->actingAs($user)->post(route('dashboard.operations.store'), [
        'amount' => 200.00,
        'date_time' => now()->format('Y-m-d\TH:i'),
        'type' => 'expense',
        'category_id' => $category->id,
        'account_id' => $account->id,
    ]);

    $response->assertSessionHasErrors(['amount']);
});
