<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockPurchase extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'purchase';
    protected $primaryKey = 'ID';

    protected $fillable = [
        'price','availableStock','boxID','supplyerID'
    ];

    public function products()
    {
        return $this->hasMany('App\Product', 'boxID', 'boxID');
    }
    public function supplyer()
    {
        return $this->belongsTo('App\Supplyer', 'supplyerID', 'id')->select(array('id', 'name','Adress','contact','total_balance','paid'));
    }

}
