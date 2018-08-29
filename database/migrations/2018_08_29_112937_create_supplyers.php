<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplyers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplyers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('total_balance')->nullable();
            $table->string('paid')->nullable();
            $table->tinyInteger('publication_status')->default(1);
            $table->string('Adress')->nullable();
            $table->string('contact')->nullable();
            $table->integer('type')->nullable();
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
        Schema::dropIfExists('supplyers');
    }
}
