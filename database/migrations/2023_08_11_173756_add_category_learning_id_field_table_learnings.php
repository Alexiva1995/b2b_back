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
        Schema::table('learnings', function (Blueprint $table) {
            $table->foreignId('category_learning_id')->nullable()->after('type');
            $table->tinyInteger('is_external')->default(0)->comment('0 - false, 1 - true')->after('category_learning_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('learnings', function (Blueprint $table) {
            $table->dropColumn('category_learning_id');
            $table->dropColumn('is_external');
        });
    }
};
