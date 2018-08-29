<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use App\ProductCategory;
use App\StockPurchase;
use App\Supplyer;
use App\Supplyerpayment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use PDF;

class StockController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
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
    public function save_purchase(Request $request) {
        $input = $request->all();
        $max = sizeof($input['data1']);
        $totalQT=0;
        $totalPrice=0;


        for($i = 0; $i < $max-1;$i++)
        {
            $data = new Product();
            $data['pName'] = $input['data1'][$i]['product'];
            $data['price'] = $input['data1'][$i]['price'];
            $data['quantity'] = $input['data1'][$i]['quantity'];

            $data['availableQty'] = $input['data1'][$i]['quantity'];

            $data['color'] = $input['data1'][$i]['color'];
            $data['size'] =  $input['data1'][$i]['size'];
            $data['boxID']=  $input['data1'][$max-1]['boxID'];
            $data['brandID'] =  $input['data1'][$i]['Brand'];
            $data['styleID'] =  $input['data1'][$i]['style'];
            $data->save();

            $totalQT=$totalQT+$input['data1'][$i]['quantity'];
            $totalPrice=$totalPrice+(ceil($input['data1'][$i]['price']*$input['data1'][$i]['quantity']));

        }
        $data2 = array();

        $data2['availableStock']=$totalQT;
        $data2['supplyerID']=$input['data1'][$max-1]['supplyer'];
        $data2['boxID']=$input['data1'][$max-1]['boxID'];
        $data2['price']=$totalPrice;
        DB::table('purchase')->insert($data2);


        $supplyerID = $input['data1'][$max-1]['supplyer'];




        $supplyerB = Supplyer::find($supplyerID);
        $supplyer = Supplyer::where('id', $supplyerID)
            ->update(['total_balance' => $supplyerB->total_balance+ $totalPrice]);


        Session::put('message', 'Purchase Successfully !');
        //return Redirect::to('add-stock');

    }
    public function save_purchaseOLD(Request $request) {
        $input = $request->all();
        $max = sizeof($input['data1']);
        $totalQT=0;
        $totalPrice=0;


        for($i = 0; $i < $max-1;$i++)
        {
            $data = new Product();
            $data['pName'] = $input['data1'][$i]['product'];
            $data['price'] = $input['data1'][$i]['price'];
            $data['quantity'] = $input['data1'][$i]['quantity'];

            $data['availableQty'] = $input['data1'][$i]['quantity'];

            $data['color'] = $input['data1'][$i]['color'];
            $data['size'] =  $input['data1'][$i]['size'];
            $data['boxID']=  $input['data1'][$max-1]['boxID'];
            $data['brandID'] =  $input['data1'][$i]['Brand'];
            $data['styleID'] =  $input['data1'][$i]['style'];

            $data->save();

            $totalQT=$totalQT+$input['data1'][$i]['quantity'];
            $totalPrice=$totalPrice+(ceil($input['data1'][$i]['price']*$input['data1'][$i]['quantity']));

        }

        $dataPurhcase = StockPurchase::where('boxID',$input['data1'][$max-1]['boxID'])->get();
        $data2 = array();

        $data2['availableStock']=$totalQT+$dataPurhcase[0]->availableStock;
        $data2['price']=$totalPrice+$dataPurhcase[0]->price;
        $data2['statusPaid']=-1;
        StockPurchase::where('boxID',$input['data1'][$max-1]['boxID'])->update($data2);

        $supplyerID = $dataPurhcase[0]->supplyerID;

        $supplyerB = Supplyer::find($supplyerID);
        $supplyer = Supplyer::where('id', $supplyerID)
            ->update(['total_balance' => $supplyerB->total_balance+ $totalPrice]);

       return $data2;

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
