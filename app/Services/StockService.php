<?php

namespace App\Services;

use App\Brand;
use App\Product;
use App\ProductCategory;
use App\StockPurchase;
use App\Supplyer;
use App\SupplyerPayment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Fetch the supporting data required for the stock purchase form.
     *
     * @return array{categories: Collection, brands: Collection}
     */
    public function getPurchaseFormData(): array
    {
        return [
            'categories' => ProductCategory::query()->orderBy('name')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
        ];
    }

    /**
     * Retrieve every recorded stock purchase with supplier information.
     */
    public function listPurchases(): Collection
    {
        return StockPurchase::with('supplyer')
            ->orderByDesc('boxID')
            ->get();
    }

    /**
     * Load a purchase invoice and its payment history.
     *
     * @return array{invoice: Collection, payments:int}
     */
    public function getInvoiceData(int $boxId): array
    {
        $invoice = StockPurchase::with(['products.styles', 'supplyer'])
            ->where('boxID', $boxId)
            ->get();

        $payments = SupplyerPayment::where('boxID', $boxId)->sum('amount');

        return [
            'invoice' => $invoice,
            'payments' => (int) $payments,
        ];
    }

    /**
     * Fetch all products for a given purchase, eager loading relationships for display.
     */
    public function getPurchaseProducts(int $boxId): Collection
    {
        return Product::query()
            ->where('boxID', $boxId)
            ->with(['brand', 'styles'])
            ->get();
    }

    /**
     * Update a product row on a purchase while keeping the parent purchase and supplier totals in sync.
     *
     * @param  array<string, mixed>  $payload
     */
    public function updateProductDetails(array $payload): void
    {
        $data = Arr::get($payload, 'data');

        if (! is_array($data)) {
            throw new ModelNotFoundException('Invalid product payload received.');
        }

        $productId = (int) Arr::get($data, 'pID');
        $purchaseId = (int) Arr::get($data, 'invoiceID');

        DB::transaction(function () use ($data, $productId, $purchaseId) {
            $product = Product::lockForUpdate()->findOrFail($productId);
            $purchase = StockPurchase::lockForUpdate()
                ->where('boxID', $purchaseId)
                ->firstOrFail();

            $updatedAttributes = [
                'availableQty' => (int) Arr::get($data, 'saleQuantity'),
                'quantity' => (int) Arr::get($data, 'Purchase'),
                'price' => (int) ceil((float) Arr::get($data, 'salePrice')),
                'color' => Arr::get($data, 'color'),
                'size' => Arr::get($data, 'size'),
                'styleID' => (int) Arr::get($data, 'style'),
                'pName' => Arr::get($data, 'pName'),
            ];

            $oldQuantity = (int) Arr::get($data, 'oldQty');
            $oldPrice = (float) Arr::get($data, 'oldPrice');
            $newQuantity = $updatedAttributes['quantity'];
            $newUnitPrice = (float) Arr::get($data, 'salePrice');

            $oldTotal = (int) ceil($oldQuantity * $oldPrice);
            $newTotal = (int) ceil($newQuantity * $newUnitPrice);

            $quantityDelta = $newQuantity - $oldQuantity;
            $priceDelta = $newTotal - $oldTotal;

            $product->update($updatedAttributes);

            $purchase->availableStock += $quantityDelta;
            $purchase->price += $priceDelta;

            if ($priceDelta > 0) {
                $purchase->statusPaid = -1;
            }

            $purchase->save();

            $supplier = Supplyer::lockForUpdate()->findOrFail((int) $purchase->supplyerID);
            $currentBalance = (int) ($supplier->total_balance ?? 0);
            $supplier->update([
                'total_balance' => $currentBalance + $priceDelta,
            ]);
        });
    }
}
