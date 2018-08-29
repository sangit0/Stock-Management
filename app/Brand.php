<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'products_brand';

    protected $fillable = [
        'name','publication_status','date','ID','brandID'
    ];



}
