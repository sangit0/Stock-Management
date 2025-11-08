<?php

namespace Tests\Feature;

use App\PaymentMethod;
use App\StockPurchase;
use App\Supplyer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function supplier_can_be_created_via_standard_form(): void
    {
        $response = $this->post('/save-supplyer', [
            'cat' => 1,
            'name' => 'Local Supplier',
            'adress' => '123 Main Street',
            'phone' => '5551234',
            'balance' => 200,
            'paid' => 50,
        ]);

        $response->assertRedirect(route('SupplyerMangement'));

        $this->assertDatabaseHas('supplyers', [
            'name' => 'Local Supplier',
            'Adress' => '123 Main Street',
            'contact' => '5551234',
            'total_balance' => 200,
            'paid' => 50,
            'type' => 1,
        ]);
    }

    /** @test */
    public function supplier_can_be_updated(): void
    {
        $supplier = Supplyer::create([
            'name' => 'Original Name',
            'total_balance' => 100,
            'paid' => 20,
            'publication_status' => 1,
            'Adress' => 'Old address',
            'contact' => '12345',
            'type' => 0,
        ]);

        $response = $this->from('/supplyer')->post('/update-supplyer', [
            'ID' => $supplier->id,
            'cat' => 1,
            'name' => 'Updated Name',
            'adress' => 'New address',
            'phone' => '98765',
            'balance' => 150,
            'paid' => 40,
        ]);

        $response->assertRedirect('/supplyer');

        $this->assertDatabaseHas('supplyers', [
            'id' => $supplier->id,
            'name' => 'Updated Name',
            'Adress' => 'New address',
            'contact' => '98765',
            'total_balance' => 150,
            'paid' => 40,
            'type' => 1,
        ]);
    }

    /** @test */
    public function supplier_can_be_created_via_ajax(): void
    {
        $response = $this->postJson('/save-supplyerAJAX', [
            'cat' => 0,
            'name' => 'Ajax Supplier',
            'address' => 'Ajax address',
            'phone' => '2223333',
            'balance' => 75,
            'paid' => 25,
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'Ajax Supplier',
                'Adress' => 'Ajax address',
                'contact' => '2223333',
            ]);

        $this->assertDatabaseHas('supplyers', [
            'name' => 'Ajax Supplier',
            'Adress' => 'Ajax address',
            'contact' => '2223333',
            'total_balance' => 75,
            'paid' => 25,
            'type' => 0,
        ]);
    }

    /** @test */
    public function recording_a_payment_updates_balances_and_purchase_status(): void
    {
        $supplier = Supplyer::create([
            'name' => 'Supplier',
            'total_balance' => 300,
            'paid' => 0,
            'publication_status' => 1,
            'Adress' => 'Payment address',
            'contact' => '0000',
            'type' => 1,
        ]);

        $paymentMethod = PaymentMethod::create([
            'Type' => 'Cash',
            'publication_status' => 1,
        ]);

        StockPurchase::create([
            'price' => 300,
            'availableStock' => 10,
            'boxID' => 1234,
            'supplyerID' => $supplier->id,
        ]);

        $response = $this->postJson('/save-supplyer-payment', [
            'data' => [
                'newpaid' => 300,
                'supplyerID' => $supplier->id,
                'paymentMethod' => $paymentMethod->ID,
                'ID' => 1234,
                'remarks' => 'Full payment',
                'paid' => 0,
                'total' => 300,
            ],
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'amount' => 300,
                'boxID' => 1234,
            ]);

        $this->assertDatabaseHas('supplyerpayments', [
            'supplyersID' => $supplier->id,
            'boxID' => 1234,
            'amount' => 300,
            'paymentMethod' => $paymentMethod->ID,
            'remarks' => 'Full payment',
        ]);

        $this->assertDatabaseHas('supplyers', [
            'id' => $supplier->id,
            'paid' => 300,
        ]);

        $this->assertDatabaseHas('purchase', [
            'boxID' => 1234,
            'statusPaid' => 0,
        ]);
    }
}
