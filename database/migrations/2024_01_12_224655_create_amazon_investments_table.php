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
        Schema::create('amazon_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amazon_category_id');
            $table->foreignId('user_id');
            $table->double('invested');
            $table->tinyInteger('status')->comment('0 - pending, 1 - active, 2 - complete, 3 - canceled, 4 - rejected');
            $table->double('gain');
            $table->foreignId('order_id');
        $table->date('date_start')->nullable();
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
        Schema::dropIfExists('amazon_investments');
    }
};
