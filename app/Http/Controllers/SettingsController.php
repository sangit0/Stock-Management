<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Http\Resources\ProductResource;
use App\Product;
use App\ProductCategory;
use App\Ship;
use App\Supplyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;

class SettingsController extends Controller
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
public function index() {
      return view('setting');
 }
 public function getBrand()
 {
     return Brand::all();
 }
// brand controller functions
 public function save_brand(Request $request) {

        $data = array();
            $data['name'] = $request->name;
            DB::table('products_brand')->insert($data);

                      return Redirect::back()->with('message', 'Information is saved Successfully !');

 }

 public function published_brand($ID) {
     DB::table('products_brand')
             ->where('ID', $ID)
            ->update(['publication_status' => 1]);

                      return Redirect::back()->with('message', 'Information is saved Successfully !');
 }


 public function unpublished_brand($ID) {
                 DB::table('products_brand')
            ->where('ID', $ID)
            ->update(['publication_status' => 0]);
                      return Redirect::back()->with('message', 'Information is saved Successfully !');
 }

 public function update_brand_info(Request $request) {
            $data = array();
           $data['name'] = $request->name;
               DB::table('products_brand')
                     ->where('ID', $request->ID)
                     ->update($data);

                     return Redirect::back()->with('message', 'Information is saved Successfully !');
					  }

}
