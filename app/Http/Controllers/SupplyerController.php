<?php

namespace App\Http\Controllers;

use App\Brand;
use App\StockPurchase;
use App\Supplyer;
use App\Supplyerpayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;

class SupplyerController extends Controller
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
        $supplyer = Supplyer::all();


        return view('Supplyer',compact('supplyer'));

    }
    public function view_supplyer($ID) {
        $supplyer = Supplyer::find($ID);
        return $supplyer;
    }
    public function update_info(Request $request) {
        $data = array();
        if($request->cat!=-1)
        $data['type'] = $request->cat;

        $data['contact'] = $request->phone;
        $data['Adress'] = $request->adress;
        $data['name'] = $request->name;
        $data['total_balance'] = $request->balance;
        $data['paid'] = $request->paid;

        Supplyer::where('id',$request->ID)
        ->update($data);



        Session::put('message','Save information successfully');
        return Redirect::back();
    }
    public function published_supplyer($ID) {

        Supplyer::find($ID)->update(['publication_status' => 1]);

        return Redirect::to('supplyer');
    }

    public function unpublished_supplyer($ID) {
        Supplyer::find($ID)->update(['publication_status' => 0]);

        return Redirect::to('supplyer');
    }

    public function save_supplyer(Request $request) {

            $data = new Supplyer();

            if($request->cat==-1) {
                Session::put('info', 'Error! please select supplyer type!');
                return Redirect::to('supplyer');

            }

            $data['type'] = $request->cat;
            $data['name'] = $request->name;
            $data['total_balance'] = $request->balance;
            $data['paid'] = $request->paid;
            $data['contact'] = $request->phone;
            $data['Adress'] = $request->adress;
            $data['publication_status'] = 1;

            $data->save();


             Session::put('message', 'Save Information Successfully !');
            return Redirect::to('supplyer');

    }
    public function save_supplyerAJAX(Request $request) {

        $data = new Supplyer();

        $data['type'] = $request->cat;
        $data['name'] = $request->name;
        $data['total_balance'] = $request->balance;
        $data['paid'] = $request->paid;
        $data['contact'] = $request->phone;
        $data['Adress'] = $request->address;
        $data['publication_status'] = 1;

        $data->save();
    }
    public function getAllsupplyer(){
        $all_published_Supplyer = \App\Supplyer::all()->where('publication_status',1);
        return $all_published_Supplyer;

    }
    public function paymentDetails($ID)
    {
        $supplyerPayment =  Supplyerpayment::where('boxID',$ID)->with('supplyer')->with('paymentMethod')->get();
       return $supplyerPayment;


    }
    public function viewPayment()
    {
        $stock = StockPurchase::with('supplyer')->orderBy('statusPaid','asc')->get();

        // return $products;

        return view('SupplyerPayment',compact('stock'));

    }
    public function save_supplyer_payment(Request $request) {


        $data = new Supplyerpayment();

        $data['amount'] = $request->data['newpaid'];
        $data['supplyersID'] = $request->data['supplyerID'];
        $data['paymentMethod'] = $request->data['paymentMethod'];
        $data['boxID'] = $request->data['ID'];
        if($data['remarks']!="")
        $data['remarks'] = $request->data['remarks'];

        $total_paid = $request->data['paid']+$data['amount'];

        $data->save();

        $supplyerB = Supplyer::find($data['supplyersID']);

        $supplyer = Supplyer::where('id', $data['supplyersID'])
            ->update(['paid' => $supplyerB->paid+ $data['amount']]);


        if($request->data['total']==$total_paid) {
            $dataStock = array();
            $dataStock['statusPaid'] = 0;

            StockPurchase::where('boxID',$data['boxID'])->update($dataStock);
        }
        else{
            $dataStock = array();
            $dataStock['statusPaid'] = -1;

            StockPurchase::where('boxID',$data['boxID'])->update($dataStock);
        }


    }




}
