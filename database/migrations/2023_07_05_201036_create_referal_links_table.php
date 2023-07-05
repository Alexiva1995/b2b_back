<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferalLinksTable extends Migration
{
    public function up()
    {
        Schema::create('referal_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('father_id');
            $table->string('link_code');
            $table->unsignedBigInteger('cyborg_id');
            $table->boolean('right')->default(false);
            $table->boolean('left')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('father_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cyborg_id')->references('id')->on('market')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('referal_links');
    }
}
