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
        Schema::table('notification_sends', function (Blueprint $table) {
            $table->string('receiver_number')->nullable()->comment('Número telefónico donde se envío el mensaje');
            $table->dropColumn('destination_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_sends', function (Blueprint $table) {
            //
        });
    }
};
