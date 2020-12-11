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
            $table->string('public_id');
            $table->bigInteger('signature_container_id')->unsigned();
            $table->foreign('signature_container_id')->references('id')->on('signature_containers');
            $table->string('identifier');
            $table->string('identifier_type');
            $table->string('country')->nullable();
            $table->json('visual_coordinates')->nullable();
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
