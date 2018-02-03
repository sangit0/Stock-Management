<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'productstyles';

    protected $fillable = [
        'name','status','created_at','update_at'
    ];
    public function products()
    {
        return $this->hasMany('App\Product', 'styleID', 'id');
    }



}
