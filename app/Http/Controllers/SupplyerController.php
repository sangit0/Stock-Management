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
session_start();

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
    public function save_supplyerAJAX(Request $request) {

        $data = new Supplyer();

        $data['type'] = $request->cat;
        $data['name'] = $request->name;
        $data['total_balance'] = $request->balance;
        $data['paid'] = $request->paid;
        $data['contact'] = $request->phone;
        $data['Adress'] = $request->address;
        $data['employeeID'] = Session::get('employeeId');
        $data['publication_status'] = 1;

        $data->save();
    }
    public function getAllsupplyer(){
        $all_published_Supplyer = \App\Supplyer::all()->where('publication_status',1);
        return $all_published_Supplyer;

    }





}
