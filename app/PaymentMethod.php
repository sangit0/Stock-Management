<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table= 'paymentmethods';

    protected $fillable = [
        'Type','publication_status'
    ];





}
