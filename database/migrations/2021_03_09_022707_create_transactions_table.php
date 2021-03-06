<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            
            $table->id();
            $table->string('date');
            $table->string('stock_code');
            $table->string('type');
            $table->double('price');
            $table->unsignedInteger('shares');
            $table->double('fees');
            $table->double('net'); 
            $table->unsignedInteger('trade_id')->index('trade_id');
            $table->integer('profile_id')->index('profile_id');
            $table->double('net_pl')->nullable();
            $table->string('remarks',99)->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
