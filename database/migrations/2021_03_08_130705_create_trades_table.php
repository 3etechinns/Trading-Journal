<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('status');
            $table->unsignedInteger('shares');
            $table->string('stock_code'); 
            $table->string('purchase_date');
            $table->string('sell_date');
            $table->double('purchase_price');
            $table->integer('sold'); 
            $table->integer('profile_id')->index('profile_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
}
