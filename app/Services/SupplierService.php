<?php

namespace App\Services;

use App\StockPurchase;
use App\Supplyer;
use App\Supplyerpayment;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    /**
     * Retrieve all suppliers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllSuppliers()
    {
        return Supplyer::all();
    }

    /**
     * Retrieve all published suppliers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPublishedSuppliers()
    {
        return Supplyer::where('publication_status', 1)->get();
    }

    /**
     * Find a supplier by id.
     *
     * @param  int  $id
     * @return \App\Supplyer
     */
    public function findSupplier(int $id)
    {
        return Supplyer::findOrFail($id);
    }

    /**
     * Create a new supplier.
     *
     * @param  array  $data
     * @return \App\Supplyer
     */
    public function createSupplier(array $data)
    {
        $supplier = new Supplyer();
        $supplier->fill($this->mapSupplierDataForCreate($data));
        $supplier->publication_status = 1;
        $supplier->save();

        return $supplier;
    }

    /**
     * Update the supplied supplier instance.
     *
     * @param  \App\Supplyer  $supplier
     * @param  array  $data
     * @return \App\Supplyer
     */
    public function updateSupplier(Supplyer $supplier, array $data)
    {
        $attributes = $this->mapSupplierDataForUpdate($data);

        if (! empty($attributes)) {
            $supplier->fill($attributes);
            $supplier->save();
        }

        return $supplier;
    }

    /**
     * Toggle the publication status for a supplier.
     *
     * @param  \App\Supplyer  $supplier
     * @param  bool  $isPublished
     * @return \App\Supplyer
     */
    public function setPublicationStatus(Supplyer $supplier, bool $isPublished)
    {
        $supplier->publication_status = $isPublished ? 1 : 0;
        $supplier->save();

        return $supplier;
    }

    /**
     * Retrieve the payment records for the provided purchase identifier.
     *
     * @param  int  $boxId
     * @return \Illuminate\Support\Collection
     */
    public function getPaymentsForPurchase(int $boxId)
    {
        return Supplyerpayment::where('boxID', $boxId)
            ->with('supplyer')
            ->with('paymentMethod')
            ->get();
    }

    /**
     * Persist a payment and update related balances inside a transaction.
     *
     * @param  array  $data
     * @return \App\Supplyerpayment
     */
    public function recordPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $supplier = $this->getSupplierForUpdate($data['supplyer_id']);

            $payment = new Supplyerpayment();
            $payment->fill([
                'amount' => (int) $data['amount'],
                'supplyersID' => $supplier->id,
                'paymentMethod' => $data['payment_method_id'] ?? null,
                'boxID' => (int) $data['box_id'],
                'remarks' => $data['remarks'] ?? null,
            ]);
            $payment->save();

            $supplier->paid = (int) $supplier->paid + (int) $data['amount'];
            $supplier->save();

            $isSettled = ((float) $data['previous_paid'] + (float) $data['amount']) >= (float) $data['total'];
            StockPurchase::where('boxID', $data['box_id'])->update([
                'statusPaid' => $isSettled ? 0 : -1,
            ]);

            return $payment->fresh(['supplyer', 'paymentMethod']);
        });
    }

    /**
     * Retrieve the supplier instance with a pessimistic lock when supported.
     *
     * @param  int  $supplierId
     * @return \App\Supplyer
     */
    protected function getSupplierForUpdate(int $supplierId)
    {
        $query = Supplyer::where('id', $supplierId);

        if ($this->connectionSupportsLocks()) {
            $query->lockForUpdate();
        }

        return $query->firstOrFail();
    }

    /**
     * Determine if the current database connection supports explicit locks.
     *
     * @return bool
     */
    protected function connectionSupportsLocks(): bool
    {
        return DB::connection()->getDriverName() !== 'sqlite';
    }

    /**
     * Map incoming payload for storing a supplier.
     *
     * @param  array  $data
     * @return array
     */
    protected function mapSupplierDataForCreate(array $data)
    {
        return [
            'type' => (int) $data['cat'],
            'name' => $data['name'],
            'total_balance' => isset($data['balance']) ? (int) $data['balance'] : 0,
            'paid' => isset($data['paid']) ? (int) $data['paid'] : 0,
            'contact' => $data['phone'] ?? null,
            'Adress' => $data['adress'] ?? null,
        ];
    }

    /**
     * Map incoming payload for updating a supplier.
     *
     * @param  array  $data
     * @return array
     */
    protected function mapSupplierDataForUpdate(array $data)
    {
        $attributes = [];

        if (array_key_exists('cat', $data) && $data['cat'] !== null && (int) $data['cat'] !== -1) {
            $attributes['type'] = (int) $data['cat'];
        }

        if (array_key_exists('name', $data)) {
            $attributes['name'] = $data['name'];
        }

        if (array_key_exists('balance', $data) && $data['balance'] !== null) {
            $attributes['total_balance'] = (int) $data['balance'];
        }

        if (array_key_exists('paid', $data) && $data['paid'] !== null) {
            $attributes['paid'] = (int) $data['paid'];
        }

        if (array_key_exists('phone', $data)) {
            $attributes['contact'] = $data['phone'];
        }

        if (array_key_exists('adress', $data)) {
            $attributes['Adress'] = $data['adress'];
        }

        return $attributes;
    }
}
