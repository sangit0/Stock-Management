<?php

namespace App\Services;

use App\Product;
use App\StockPurchase;
use App\Supplyer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * Persist a brand-new purchase with the provided products.
     */
    public function createPurchase(array $data): StockPurchase
    {
        return DB::transaction(function () use ($data) {
            $totals = $this->persistProducts($data['products'], (int) $data['box_id']);

            $purchase = StockPurchase::create([
                'availableStock' => $totals['quantity'],
                'price' => $totals['price'],
                'boxID' => $data['box_id'],
                'supplyerID' => $data['supplier_id'],
            ]);

            $this->adjustSupplierBalance((int) $data['supplier_id'], $totals['price']);

            return $purchase->fresh(['products', 'supplyer']);
        });
    }

    /**
     * Append additional products to an existing purchase.
     */
    public function appendProducts(array $data): StockPurchase
    {
        return DB::transaction(function () use ($data) {
            /** @var StockPurchase $purchase */
            $purchase = StockPurchase::where('boxID', $data['box_id'])
                ->lockForUpdate()
                ->first();

            if (! $purchase) {
                throw new ModelNotFoundException('Purchase invoice not found.');
            }

            $totals = $this->persistProducts($data['products'], (int) $purchase->boxID);

            $purchase->availableStock += $totals['quantity'];
            $purchase->price += $totals['price'];
            $purchase->statusPaid = -1;
            $purchase->save();

            $this->adjustSupplierBalance((int) $purchase->supplyerID, $totals['price']);

            return $purchase->fresh(['products', 'supplyer']);
        });
    }

    /**
     * Persist each product row and calculate aggregate totals.
     *
     * @param  array<int, array<string, mixed>>  $products
     * @return array{quantity:int, price:int}
     */
    protected function persistProducts(array $products, int $boxId): array
    {
        $totalQuantity = 0;
        $totalPrice = 0;

        foreach ($products as $productData) {
            $attributes = $this->mapProductAttributes($productData, $boxId);

            $product = new Product();
            $product->pName = $attributes['pName'];
            $product->price = $attributes['price'];
            $product->quantity = $attributes['quantity'];
            $product->availableQty = $attributes['availableQty'];
            $product->color = $attributes['color'];
            $product->size = $attributes['size'];
            $product->boxID = $attributes['boxID'];
            $product->brandID = $attributes['brandID'];
            $product->styleID = $attributes['styleID'];
            $product->save();

            $totalQuantity += $attributes['quantity'];
            $totalPrice += $attributes['totalPrice'];
        }

        return [
            'quantity' => $totalQuantity,
            'price' => $totalPrice,
        ];
    }

    /**
     * Normalise a product payload into attributes ready for persistence.
     *
     * @param  array<string, mixed>  $product
     * @return array<string, int|string|null>
     */
    protected function mapProductAttributes(array $product, int $boxId): array
    {
        $quantity = (int) $product['quantity'];
        $unitPrice = (float) $product['price'];

        return [
            'pName' => Arr::get($product, 'product'),
            'price' => (int) ceil($unitPrice),
            'quantity' => $quantity,
            'availableQty' => $quantity,
            'color' => Arr::get($product, 'color'),
            'size' => Arr::get($product, 'size'),
            'boxID' => (string) $boxId,
            'brandID' => (int) Arr::get($product, 'Brand'),
            'styleID' => (int) Arr::get($product, 'style'),
            'totalPrice' => (int) ceil($unitPrice * $quantity),
        ];
    }

    /**
     * Increment the supplier balance while inside an open transaction.
     */
    protected function adjustSupplierBalance(int $supplierId, int $amount): void
    {
        $supplier = Supplyer::lockForUpdate()->findOrFail($supplierId);
        $currentBalance = (int) ($supplier->total_balance ?? 0);

        $supplier->update([
            'total_balance' => $currentBalance + $amount,
        ]);
    }
}
