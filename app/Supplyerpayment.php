<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplyerpayment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'amount','supplyersID','remarks','paymentMethod','boxID','statusPaid'
    ];


    public function supplyer()
    {
        return $this->belongsTo('App\Supplyer', 'supplyersID', 'id')->select(array('id', 'name','total_balance','paid'));
    }
    public function paymentMethod()
    {
        return $this->belongsTo('App\PaymentMethod', 'paymentMethod', 'ID')->select(array('ID', 'Type'));
    }



}
