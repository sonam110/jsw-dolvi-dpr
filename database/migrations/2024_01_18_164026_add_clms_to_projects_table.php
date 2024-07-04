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
        Schema::table('projects', function (Blueprint $table) {
            $table->longText('dpr_report_emails ')->after('description')->comment('for dpr reports project wise')->nullable();
            $table->longText('reminder_emails')->after('dpr_report_emails ')->comment('for reminder emails')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('emails');
            $table->dropColumn('other_emails');
        });
    }
};
