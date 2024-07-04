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
        Schema::table('item_description_masters', function (Blueprint $table) {
            $table->string('unit_of_measure')->after('title')->nullable();
            $table->string('man_power_type')->after('unit_of_measure')->nullable();
            $table->integer('orderno')->after('man_power_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_description_masters', function (Blueprint $table) {
            $table->dropColumn('unit_of_measure');
            $table->dropColumn('man_power_type');
            $table->dropColumn('orderno');
        });
    }
};
