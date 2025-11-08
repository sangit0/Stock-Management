<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierPaymentRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Services\SupplierService;
use App\StockPurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplyerController extends Controller
{
    /**
     * @var \App\Services\SupplierService
     */
    protected $supplierService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\SupplierService  $supplierService
     * @return void
     */
    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of suppliers.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $supplyer = $this->supplierService->getAllSuppliers();

        return view('supplyer', compact('supplyer'));
    }

    /**
     * Display the specified supplier.
     *
     * @param  int  $ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function view_supplyer(int $ID): JsonResponse
    {
        $supplier = $this->supplierService->findSupplier($ID);

        return response()->json($supplier);
    }

    /**
     * Update the specified supplier in storage.
     *
     * @param  \App\Http\Requests\UpdateSupplierRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_info(UpdateSupplierRequest $request): RedirectResponse
    {
        $supplier = $this->supplierService->findSupplier((int) $request->input('ID'));
        $this->supplierService->updateSupplier($supplier, $request->validated());

        return redirect()->back()->with('message', 'Save information successfully');
    }

    /**
     * Mark the supplier as published.
     *
     * @param  int  $ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function published_supplyer(int $ID): RedirectResponse
    {
        $supplier = $this->supplierService->findSupplier($ID);
        $this->supplierService->setPublicationStatus($supplier, true);

        return redirect()->route('SupplyerMangement');
    }

    /**
     * Mark the supplier as unpublished.
     *
     * @param  int  $ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unpublished_supplyer(int $ID): RedirectResponse
    {
        $supplier = $this->supplierService->findSupplier($ID);
        $this->supplierService->setPublicationStatus($supplier, false);

        return redirect()->route('SupplyerMangement');
    }

    /**
     * Store a newly created supplier in storage.
     *
     * @param  \App\Http\Requests\StoreSupplierRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_supplyer(StoreSupplierRequest $request): RedirectResponse
    {
        $this->supplierService->createSupplier($request->validated());

        return redirect()->route('SupplyerMangement')->with('message', 'Save Information Successfully !');
    }

    /**
     * Store a newly created supplier via AJAX.
     *
     * @param  \App\Http\Requests\StoreSupplierRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_supplyerAJAX(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->createSupplier($request->validated());

        return response()->json($supplier, 201);
    }

    /**
     * Retrieve all published suppliers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllsupplyer(): JsonResponse
    {
        $publishedSuppliers = $this->supplierService->getPublishedSuppliers();

        return response()->json($publishedSuppliers);
    }

    /**
     * Retrieve payment details for the given purchase identifier.
     *
     * @param  int  $ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentDetails(int $ID): JsonResponse
    {
        $supplyerPayment = $this->supplierService->getPaymentsForPurchase($ID);

        return response()->json($supplyerPayment);
    }

    /**
     * Display the supplier payment view.
     *
     * @return \Illuminate\View\View
     */
    public function viewPayment(): View
    {
        $stock = StockPurchase::with('supplyer')->orderBy('statusPaid', 'asc')->get();

        return view('supplyer_payment', compact('stock'));
    }

    /**
     * Store a supplier payment.
     *
     * @param  \App\Http\Requests\StoreSupplierPaymentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_supplyer_payment(StoreSupplierPaymentRequest $request): JsonResponse
    {
        $payment = $this->supplierService->recordPayment($request->validated());

        return response()->json($payment, 201);
    }
}
