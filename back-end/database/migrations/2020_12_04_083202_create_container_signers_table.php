<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainerSignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_signers', function (Blueprint $table) {
            $table->id();
            $table->string('signature_status')->default('pending'); // signing,
            $table->string('country');
            $table->string('idcode');
            $table->bigInteger('signature_container_id')->unsigned();
            $table->foreign('signature_container_id')->references('id')->on('signature_containers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_signers');
    }
}
