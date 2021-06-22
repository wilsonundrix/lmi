<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStkPushesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stk_pushes', function (Blueprint $table) {
            $table->id();
            $table->string('merchantRequestID')->nullable();
            $table->string('checkoutRequestID')->nullable();
            $table->string('requestCode')->nullable();
            $table->string('requestDesc')->nullable();
            $table->string('mpesaReceiptNumber')->nullable();
            $table->string('pesa')->nullable();
            $table->string('b2cUtilityAccountAvailableFunds')->nullable();
            $table->string('amount')->nullable();
            $table->string('balance')->nullable();
            $table->string('transactionDate')->nullable();
            $table->string('phoneNumber')->nullable();
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
        Schema::dropIfExists('stk_pushes');
    }
}
