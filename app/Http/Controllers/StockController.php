<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Product;
use App\ProductCategory;
use App\Services\PurchaseService;
use App\StockPurchase;
use App\Supplyer;
use App\Supplyerpayment;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PDF;
use Session;

class StockController extends Controller
{
    protected $purchaseService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PurchaseService $purchaseService) {
        $this->purchaseService = $purchaseService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = ProductCategory::all();
        $brand = Brand::all();
        return view('add_purchase',compact('products'),compact('brand'));

    }
    public function save_purchase(StorePurchaseRequest $request): JsonResponse {
        $purchase = $this->purchaseService->createPurchase($request->validated());

        Session::put('message', 'Purchase Successfully !');

        return response()->json([
            'message' => 'Purchase created successfully.',
            'purchase' => $purchase,
        ], 201);

    }
    public function save_purchaseOLD(UpdatePurchaseRequest $request): JsonResponse {
        $purchase = $this->purchaseService->appendProducts($request->validated());

        return response()->json([
            'message' => 'Purchase updated successfully.',
            'purchase' => $purchase,
        ]);

    }
    public function view()
    {
        $products = StockPurchase::with('supplyer')->orderBy('boxID','desc')->get();

       // return $products;

        return view('Stock',compact('products'));

    }
    public function pdf($ID){
        $Invoice = StockPurchase::with(['products.styles','supplyer'])->where('boxID',$ID)->get();
        $paymenthist= Supplyerpayment::where('boxID',$ID)->sum('amount');
        //return view("PDF.pdfstock",compact(['Invoice','paymenthist']));

        $pdf = PDF::loadView('PDF.pdfstock',compact(['Invoice','paymenthist']));
        return $pdf->stream('Purchase_invoice_'.$ID.'.pdf');
    }
    public function viewDetails($ID)
    {
        $products = Product::where('boxID',$ID)->with(['brand','styles'])->get();

         //return $products;

        return $products;

    }
    public function updateproductDetails(Request $request)
    {
        $input = $request->all();

        //  return $input['data'];

            $data = array();
            $data['availableQty'] = $input['data']['saleQuantity'];
            $data['quantity'] = $input['data']['Purchase'];
            $data['price'] = $input['data']['salePrice'];
            $data['color'] = $input['data']['color'];
            $data['size'] = $input['data']['size'];
            $data['styleID'] = $input['data']['style'];
            $data['pName'] = $input['data']['pName'];

            $total = ceil($input['data']['Purchase'] * $input['data']['salePrice']);

            $totalOLD = ceil($input['data']['oldQty'] * $input['data']['oldPrice']);


            Product::where('ID', $input['data']['pID'])->update($data);

            $dataPurchase = StockPurchase::where('boxID',$input['data']['invoiceID'])->get();
            //return $dataPurchase[0]->price;
            if($totalOLD>=$total){
            StockPurchase::where('boxID', $input['data']['invoiceID'])
            ->update(['price' => $dataPurchase[0]->price - ($totalOLD- $total),
            'availableStock' => $dataPurchase[0]->availableStock - ($input['data']['oldQty']-$input['data']['Purchase'])]);
            }
            else{
                StockPurchase::where('boxID', $input['data']['invoiceID'])
                    ->update(['price' => $dataPurchase[0]->price - ($totalOLD- $total),'statusPaid'=>-1,
                        'availableStock' => $dataPurchase[0]->availableStock - ($input['data']['oldQty']-$input['data']['Purchase'])]);
            }

            $supplyer = Supplyer::find($dataPurchase[0]->supplyerID);
            Supplyer::where('id', $dataPurchase[0]->supplyerID)
                ->update(['total_balance' => $supplyer->total_balance - ($totalOLD - $total)]
                );


            Session::put('message', 'Stock Invoice#' . $input['data']['invoiceID'] . ' Updated Successfully!');



    }


}
