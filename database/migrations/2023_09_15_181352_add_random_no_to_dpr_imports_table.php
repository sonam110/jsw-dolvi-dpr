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
        Schema::table('dpr_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('item_desc_id')->after('item_desc')->nullable();
            $table->string('random_no','15')->after('item_desc_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dpr_imports', function (Blueprint $table) {
            $table->dropColumn('item_desc_id');
            $table->dropColumn('random_no');
        });
    }
};
