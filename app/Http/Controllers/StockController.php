<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Services\PurchaseService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PDF;

class StockController extends Controller
{
    /**
     * @var PurchaseService
     */
    protected $purchaseService;

    /**
     * @var StockService
     */
    protected $stockService;

    /**
     * Create a new controller instance.
     */
    public function __construct(PurchaseService $purchaseService, StockService $stockService)
    {
        $this->purchaseService = $purchaseService;
        $this->stockService = $stockService;
    }

    /**
     * Show the stock purchase entry form.
     */
    public function index()
    {
        $formData = $this->stockService->getPurchaseFormData();

        return view('add_purchase', [
            'products' => $formData['categories'],
            'brand' => $formData['brands'],
        ]);
    }

    /**
     * Persist a brand-new purchase and return the stored record.
     */
    public function save_purchase(StorePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->createPurchase($request->validated());

        Session::flash('message', 'Purchase Successfully !');

        return response()->json([
            'message' => 'Purchase created successfully.',
            'purchase' => $purchase,
        ], 201);
    }

    /**
     * Append products to an existing purchase invoice.
     */
    public function save_purchaseOLD(UpdatePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->appendProducts($request->validated());

        return response()->json([
            'message' => 'Purchase updated successfully.',
            'purchase' => $purchase,
        ]);
    }

    /**
     * Display a listing of recorded stock purchases.
     */
    public function view()
    {
        $purchases = $this->stockService->listPurchases();

        return view('stock', ['products' => $purchases]);
    }

    /**
     * Stream the generated PDF invoice for the requested purchase.
     */
    public function pdf($ID)
    {
        $invoiceData = $this->stockService->getInvoiceData((int) $ID);

        $pdf = PDF::loadView('PDF.pdfstock', [
            'Invoice' => $invoiceData['invoice'],
            'paymenthist' => $invoiceData['payments'],
        ]);

        return $pdf->stream('Purchase_invoice_' . $ID . '.pdf');
    }

    /**
     * Return the products associated with a purchase invoice.
     */
    public function viewDetails($ID)
    {
        $products = $this->stockService->getPurchaseProducts((int) $ID);

        return response()->json($products);
    }

    /**
     * Update a product row and keep the related purchase consistent.
     */
    public function updateproductDetails(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|array',
            'data.invoiceID' => 'required|integer',
            'data.pID' => 'required|integer',
            'data.saleQuantity' => 'required|integer',
            'data.Purchase' => 'required|integer',
            'data.salePrice' => 'required|numeric',
            'data.color' => 'nullable|string|max:255',
            'data.size' => 'nullable|string|max:255',
            'data.style' => 'required|integer',
            'data.pName' => 'required|string|max:255',
            'data.oldQty' => 'required|integer',
            'data.oldPrice' => 'required|numeric',
        ]);

        $this->stockService->updateProductDetails($validated);

        Session::flash('message', 'Stock Invoice#' . $validated['data']['invoiceID'] . ' Updated Successfully!');

        return response()->json([
            'message' => 'Stock invoice updated successfully.',
        ]);
    }
}
