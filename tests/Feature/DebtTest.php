<?php

use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('debts page loads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.debts.index'));

    $response->assertStatus(200);
});

test('create debt', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.debts.store'), [
        'lender' => 'Bank of America',
        'total_amount' => 10000,
        'start_date' => '2025-01-01',
        'end_date' => '2026-01-01',
    ]);

    $response->assertRedirect(route('dashboard.debts.index'));
    $response->assertSessionHas('success', 'Deuda creada correctamente');

    $this->assertDatabaseHas('debts', [
        'lender' => 'Bank of America',
        'total_amount' => 10000,
        'user_id' => $user->id,
    ]);
});

test('update debt', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'lender' => 'Old Lender',
    ]);

    $response = $this->actingAs($user)->put(route('dashboard.debts.update', $debt), [
        'lender' => 'Updated Lender',
        'total_amount' => 20000,
        'start_date' => '2025-01-01',
        'end_date' => '2027-01-01',
    ]);

    $response->assertRedirect(route('dashboard.debts.index'));
    $response->assertSessionHas('success', 'Deuda actualizada correctamente');

    $this->assertDatabaseHas('debts', [
        'id' => $debt->id,
        'lender' => 'Updated Lender',
        'total_amount' => 20000,
    ]);
});

test('delete debt', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(route('dashboard.debts.destroy', $debt));

    $response->assertRedirect(route('dashboard.debts.index'));
    $response->assertSessionHas('success', 'Deuda eliminada correctamente');

    $this->assertDatabaseMissing('debts', [
        'id' => $debt->id,
    ]);
});

test('add payment to debt', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'total_amount' => 10000,
        'paid_amount' => 0,
    ]);

    $response = $this->actingAs($user)->post(route('dashboard.debts.payment', $debt), [
        'payment_amount' => 5000,
    ]);

    $response->assertRedirect(route('dashboard.debts.index'));
    $response->assertSessionHas('success', 'Pago añadido correctamente');

    $this->assertDatabaseHas('debts', [
        'id' => $debt->id,
        'paid_amount' => 5000,
    ]);
});

test('mark debt as paid', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'total_amount' => 10000,
        'paid_amount' => 0,
        'status' => 'no_paid',
    ]);

    $response = $this->actingAs($user)->post(route('dashboard.debts.paid', $debt));

    $response->assertRedirect(route('dashboard.debts.index'));
    $response->assertSessionHas('success', 'Deuda marcada como pagada');

    $this->assertDatabaseHas('debts', [
        'id' => $debt->id,
        'status' => 'paid',
        'paid_amount' => 10000,
    ]);
});

test('validation errors', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.debts.store'), []);

    $response->assertSessionHasErrors(['lender', 'total_amount', 'start_date', 'end_date']);
});

test('create debt via ajax returns json', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('dashboard.debts.store'), [
        'lender' => 'Bank of America',
        'total_amount' => 10000,
        'start_date' => '2025-01-01',
        'end_date' => '2026-01-01',
    ]);

    $response->assertCreated()->assertJson([
        'message' => 'Deuda creada correctamente',
    ]);
});

test('update debt via ajax returns json', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'lender' => 'Old Lender',
    ]);

    $response = $this->actingAs($user)->putJson(route('dashboard.debts.update', $debt), [
        'lender' => 'Updated Lender',
        'total_amount' => 20000,
        'start_date' => '2025-01-01',
        'end_date' => '2027-01-01',
    ]);

    $response->assertOk()->assertJson([
        'message' => 'Deuda actualizada correctamente',
    ]);
});

test('delete debt via ajax returns json', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->deleteJson(route('dashboard.debts.destroy', $debt));

    $response->assertOk()->assertJson([
        'message' => 'Deuda eliminada correctamente',
    ]);
});

test('add payment to debt via ajax returns json', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'total_amount' => 10000,
        'paid_amount' => 0,
        'status' => 'no_paid',
    ]);

    $response = $this->actingAs($user)->postJson(route('dashboard.debts.payment', $debt), [
        'payment_amount' => 5000,
    ]);

    $response->assertOk()->assertJson([
        'message' => 'Pago añadido correctamente',
    ]);

    $this->assertDatabaseHas('debts', [
        'id' => $debt->id,
        'paid_amount' => 5000,
    ]);
});

test('debt payment cannot exceed remaining amount (ajax)', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'total_amount' => 100,
        'paid_amount' => 0,
        'status' => 'no_paid',
    ]);

    $response = $this->actingAs($user)->postJson(route('dashboard.debts.payment', $debt), [
        'payment_amount' => 200,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['payment_amount']);
});

test('cannot add payment to other user debt', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $otherUser->id,
        'total_amount' => 100,
        'paid_amount' => 0,
        'status' => 'no_paid',
    ]);

    $response = $this->actingAs($user)->postJson(route('dashboard.debts.payment', $debt), [
        'payment_amount' => 10,
    ]);

    $response->assertForbidden();
});
