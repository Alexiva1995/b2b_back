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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('package_id');
            $table->foreignId('order_id');
            $table->double('capital');
            $table->double('invested');
            $table->date('expiration_date');
            $table->double('gain');
            $table->double('max_gain');
            $table->tinyInteger('status')->default(0)->comment('0 - pending, 1 - active, 2 - completed, 3 - cancelled');
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
        Schema::dropIfExists('investment');
    }
};
