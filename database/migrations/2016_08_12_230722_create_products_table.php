<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_products', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('pName')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('boxID');
            $table->integer('quantity');
            $table->integer('price')->default(0);;
            $table->integer('availableQty')->default(0);
            $table->integer('styleID');
            $table->integer('brandID');
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
        Schema::drop('tbl_products');
    }
}
