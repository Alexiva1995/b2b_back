<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coinpayment_transactions', function (Blueprint $table) {
            $table->integer('finish_pay')->default(0)->comment('0 - usuario aun no indica pago listo, 1 - usuario indica pago listo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coinpayment_transactions', function (Blueprint $table) {
            $table->dropColumn('finish_pay');
        });
    }
};
