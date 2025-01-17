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
        Schema::table('dpr_maps', function (Blueprint $table) {
            $table->text('item_desc')->after('slug')->nullable();
            $table->unsignedBigInteger('item_desc_id')->after('item_desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dpr_maps', function (Blueprint $table) {
            $table->dropColumn('item_desc');
            $table->dropColumn('item_desc_id');
        });
    }
};
