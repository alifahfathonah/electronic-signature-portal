<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableSignatureContainerUserVisualSignatureData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signature_container_user', function (Blueprint $table) {
            $table->json('visual_coordinates')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signature_container_user', function (Blueprint $table) {
            $table->dropColumn('visual_coordinates');
        });
    }
}
