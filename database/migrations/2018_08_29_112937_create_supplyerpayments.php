<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplyerpayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplyerpayments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amount')->nullable();
            $table->integer('supplyersID')->nullable();
            $table->tinyInteger('paymentMethod')->nullable();
            $table->integer('boxID')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplyerpayments');
    }
}
