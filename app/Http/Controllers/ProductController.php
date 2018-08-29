<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Product;
use App\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {


    }


    public function get()
    {
        $products = ProductCategory::all();
        return $products;

    }
    public function save(Request $request)
    {
        $style = new ProductCategory();
        $style['name'] = $request->data;
        $style->save();

    }
    public function update(Request $request)
    {
        $id = $request->data['id'];
        $name = $request->data['name'];

        ProductCategory::find($id)->update(['name' => $name]);

    }
    public function publish($ID)
    {
        ProductCategory::find($ID)->update(['status' => 1]);
        return Redirect::to('settings');

    }
    public function unpublish($ID)
    {
        ProductCategory::find($ID)->update(['status' => 0]);
        return Redirect::to('settings');

    }
    public function getProductAll(){
        $all_published_product = \App\Product::where('availableQty','!=',0)->with(['brand','styles','stockID'])->get();
        return $all_published_product;
    }
}
