<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplyer extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey ='id';


    protected $fillable = [
        'name','publication_status','total_balance','paid','Adress','contact','type'
    ];

    public function stock()
    {
        return $this->hasMany('App\StockPurchase', 'supplyerID', 'id');
    }




}
