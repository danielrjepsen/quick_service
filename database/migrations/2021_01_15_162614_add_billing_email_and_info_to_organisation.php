<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingEmailAndInfoToOrganisation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisation', function (Blueprint $table) {
            $table->string('billing_email')->nullable();
            $table->text('billing_info')->nullable();
            $table->boolean('require2FA')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisation', function (Blueprint $table) {
            $table->dropColumn('billing_email');
            $table->dropColumn('billing_info');
            $table->dropColumn('require2FA');
        });
    }
}
