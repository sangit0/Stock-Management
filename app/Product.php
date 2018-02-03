<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'tbl_products';



    public function stock()
    {
        return $this->belongsTo('App\StockPurchase', 'id', 'boxID');
    }
    public function brand()
    {
        return $this->belongsTo('App\Brand', 'brandID', 'ID')->select(array('ID', 'name'));
    }
    public function styles()
    {
        return $this->belongsTo('App\ProductCategory', 'styleID', 'id')->select(array('id', 'name'));
    }
    public function stockID()
    {
        return $this->belongsTo('App\StockPurchase', 'id', 'boxID')->select(array('boxID'));
    }






}
