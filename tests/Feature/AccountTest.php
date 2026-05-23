<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('accounts page loads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.accounts.index'));

    $response->assertStatus(200);
});

test('create account', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.accounts.store'), [
        'name' => 'Test Account',
        'type' => 'cash',
        'current_balance' => 1000.00,
        'credit_limit' => null,
    ]);

    $response->assertRedirect(route('dashboard.accounts.index'));
    $response->assertSessionHas('success', 'Cuenta creada correctamente');

    $this->assertDatabaseHas('accounts', [
        'name' => 'Test Account',
        'type' => 'cash',
        'user_id' => $user->id,
    ]);
});

test('create credit card account with credit limit', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.accounts.store'), [
        'name' => 'Credit Card Account',
        'type' => 'credit_card',
        'current_balance' => 0,
        'credit_limit' => 5000.00,
    ]);

    $response->assertRedirect(route('dashboard.accounts.index'));

    $this->assertDatabaseHas('accounts', [
        'name' => 'Credit Card Account',
        'type' => 'credit_card',
        'credit_limit' => 5000.00,
    ]);
});

test('credit limit required for credit card type', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.accounts.store'), [
        'name' => 'Credit Card Without Limit',
        'type' => 'credit_card',
        'current_balance' => 0,
        'credit_limit' => null,
    ]);

    $response->assertSessionHasErrors(['credit_limit']);
});

test('update account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old Name',
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.accounts.update', $account), [
        'name' => 'Updated Name',
        'type' => 'checking_account',
        'current_balance' => 5000.00,
    ]);

    $response->assertRedirect(route('dashboard.accounts.index'));
    $response->assertSessionHas('success', 'Cuenta actualizada correctamente');

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'name' => 'Updated Name',
    ]);
});

test('delete account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('dashboard.accounts.destroy', $account));

    $response->assertRedirect(route('dashboard.accounts.index'));
    $response->assertSessionHas('success', 'Cuenta eliminada correctamente');

    $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
});

test('cannot delete account with operations', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $account = Account::factory()->create(['user_id' => $user->id]);

    Operation::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'account_id' => $account->id,
        'type' => 'expense',
        'amount' => 10.00,
    ]);

    $this->actingAs($user)
        ->delete(route('dashboard.accounts.destroy', $account))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('accounts', ['id' => $account->id]);
});

test('validation errors on create account', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.accounts.store'), []);

    $response->assertSessionHasErrors(['name', 'type', 'current_balance']);
});

test('validation errors on update account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('dashboard.accounts.update', $account), [
        'name' => '',
        'type' => 'invalid',
    ]);

    $response->assertSessionHasErrors(['name', 'type']);
});

test('type must be valid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.accounts.store'), [
        'name' => 'Invalid Type Account',
        'type' => 'invalid_type',
        'current_balance' => 100,
    ]);

    $response->assertSessionHasErrors(['type']);
});

test('user cannot update another user account', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->put(route('dashboard.accounts.update', $account), [
        'name' => 'Hacked Name',
        'type' => 'cash',
        'current_balance' => 1000,
    ]);

    $response->assertStatus(403);
});

test('user cannot delete another user account', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->delete(route('dashboard.accounts.destroy', $account));

    $response->assertStatus(403);
});

test('accounts are filtered by user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userAccount = Account::factory()->create(['user_id' => $user->id, 'name' => 'User Account']);
    Account::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other User Account']);

    $response = $this->actingAs($user)->get(route('dashboard.accounts.index'));

    $response->assertViewHas('accounts', function ($accounts) {
        $names = collect($accounts->items())->pluck('name')->all();

        return in_array('User Account', $names, true)
            && ! in_array('Other User Account', $names, true);
    });
});

test('search filters accounts by name', function () {
    $user = User::factory()->create();
    Account::factory()->create(['user_id' => $user->id, 'name' => 'Bank Account']);
    Account::factory()->create(['user_id' => $user->id, 'name' => 'Other Unique Account Name']);

    $response = $this->actingAs($user)->get(route('dashboard.accounts.index', ['search' => 'Bank']));

    $response->assertViewHas('accounts', function ($accounts) {
        $names = collect($accounts->items())->pluck('name')->all();

        return in_array('Bank Account', $names, true)
            && ! in_array('Other Unique Account Name', $names, true);
    });
});

test('color and icon are assigned automatically based on type', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.accounts.store'), [
        'name' => 'Cash Account',
        'type' => 'cash',
        'current_balance' => 500,
    ]);

    $account = Account::where('name', 'Cash Account')->first();

    expect($account->color)->toBe('#22C55E');
    expect($account->icon)->toBe('fas fa-money-bill');
});

test('color and icon change when type changes', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'type' => 'cash',
    ]);

    $this->actingAs($user)->put(route('dashboard.accounts.update', $account), [
        'name' => $account->name,
        'type' => 'savings_account',
        'current_balance' => $account->current_balance,
    ]);

    $account->refresh();

    expect($account->color)->toBe('#A855F7');
    expect($account->icon)->toBe('fas fa-piggy-bank');
});
