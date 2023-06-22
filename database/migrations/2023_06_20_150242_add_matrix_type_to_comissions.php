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
        Schema::table('wallets_commissions', function (Blueprint $table) {
            $table->integer('type_matrix')->comment('0 - Matrix 1 , 1 - Matrix 2 , 2 - Matrix 3, 3 - Matrix 4 , 4 - Matrix 5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallets_commissions', function (Blueprint $table) {
            $table->dropColumn('type_matrix');
        });
    }
};
