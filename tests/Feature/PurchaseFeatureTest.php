<?php

namespace Tests\Feature;

use App\Brand;
use App\Product;
use App\ProductCategory;
use App\StockPurchase;
use App\Supplyer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_purchase_and_updates_supplier_balance(): void
    {
        $supplier = Supplyer::create([
            'name' => 'Supplier One',
            'total_balance' => 0,
        ]);

        $brand = Brand::create(['name' => 'Brand One']);
        $style = ProductCategory::create(['name' => 'Style One']);

        $payload = [
            'data1' => [
                [
                    'product' => 'Sample Product',
                    'price' => 12.5,
                    'quantity' => 2,
                    'color' => 'Red',
                    'size' => 'M',
                    'Brand' => $brand->ID,
                    'style' => $style->id,
                ],
                [
                    'boxID' => 1001,
                    'supplyer' => $supplier->id,
                ],
            ],
        ];

        $response = $this->json('POST', '/save-purchase', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'purchase' => ['ID', 'availableStock', 'price', 'boxID', 'supplyerID'],
            ]);

        $purchase = StockPurchase::first();
        $this->assertNotNull($purchase);
        $this->assertEquals(2, $purchase->availableStock);
        $this->assertEquals(25, $purchase->price);
        $this->assertCount(1, $purchase->products);

        $this->assertEquals(25, $supplier->fresh()->total_balance);
    }

    /** @test */
    public function it_appends_products_to_an_existing_purchase(): void
    {
        $supplier = Supplyer::create([
            'name' => 'Supplier Two',
            'total_balance' => 0,
        ]);

        $brand = Brand::create(['name' => 'Brand Two']);
        $style = ProductCategory::create(['name' => 'Style Two']);

        $initialPayload = [
            'data1' => [
                [
                    'product' => 'Initial Product',
                    'price' => 10,
                    'quantity' => 3,
                    'Brand' => $brand->ID,
                    'style' => $style->id,
                ],
                [
                    'boxID' => 2002,
                    'supplyer' => $supplier->id,
                ],
            ],
        ];

        $this->json('POST', '/save-purchase', $initialPayload)->assertStatus(201);

        $updatePayload = [
            'data1' => [
                [
                    'product' => 'New Product',
                    'price' => 5,
                    'quantity' => 4,
                    'Brand' => $brand->ID,
                    'style' => $style->id,
                ],
                [
                    'boxID' => 2002,
                ],
            ],
        ];

        $response = $this->json('POST', '/save-purchase-old-invoice', $updatePayload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'purchase' => ['ID', 'availableStock', 'price', 'boxID', 'supplyerID'],
            ]);

        $purchase = StockPurchase::where('boxID', 2002)->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(7, $purchase->availableStock);
        $this->assertEquals(50, $purchase->price);
        $this->assertCount(2, $purchase->products);
        $this->assertEquals(50, $supplier->fresh()->total_balance);
    }

    /** @test */
    public function it_rolls_back_if_any_product_persist_fails(): void
    {
        $supplier = Supplyer::create([
            'name' => 'Supplier Three',
            'total_balance' => 0,
        ]);

        $brand = Brand::create(['name' => 'Brand Three']);
        $style = ProductCategory::create(['name' => 'Style Three']);

        $payload = [
            'data1' => [
                [
                    'product' => 'Broken Product',
                    'price' => 20,
                    'quantity' => 1,
                    'Brand' => $brand->ID,
                    'style' => $style->id,
                ],
                [
                    'boxID' => 3003,
                    'supplyer' => $supplier->id,
                ],
            ],
        ];

        Event::listen('eloquent.creating: App\\Product', function () {
            throw new \RuntimeException('Force failure');
        });

        $response = $this->json('POST', '/save-purchase', $payload);
        $response->assertStatus(500);

        $this->assertEquals(0, Product::count());
        $this->assertEquals(0, StockPurchase::count());
        $this->assertEquals(0, $supplier->fresh()->total_balance);

        Event::forget('eloquent.creating: App\\Product');
    }
}
