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
        Schema::table('category_learnings', function (Blueprint $table) {
            $table->tinyInteger('is_top')->default(0);
            $table->date('date_top')->nullable();
        });
        Schema::table('learnings', function (Blueprint $table) {
            $table->tinyInteger('is_top')->default(0);
            $table->date('date_top')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_learnings', function (Blueprint $table) {
            $table->dropColumn('is_top');
            $table->dropColumn('date_top');
        });
        Schema::table('learnings', function (Blueprint $table) {
            $table->dropColumn('is_top');
            $table->dropColumn('date_top');
        });
    }
};
