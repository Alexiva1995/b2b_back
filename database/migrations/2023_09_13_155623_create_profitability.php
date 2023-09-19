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
        Schema::create('profitability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('liquidation_id')->nullable();
            $table->foreignId('invest_id')->constrained('investments');
            $table->double('amount');
            $table->tinyInteger('status')->default(0)->comment('0 - disponible, 1 - solicitado, 2 - pagado, 3- anulado, 4-pendiente');
            $table->double('amount_retired')->nullable();
            $table->double('amount_available')->nullable();
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
        Schema::dropIfExists('profitability');
    }
};
