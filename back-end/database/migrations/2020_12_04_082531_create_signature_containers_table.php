<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignatureContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signature_containers', function (Blueprint $table) {
            $table->id();
            $table->string('container_type')->default('asice'); // Or pdf
            $table->string('container_path')->nullable();
            $table->string('public_id');
            $table->bigInteger('company_user_id')->unsigned();
            $table->foreign('company_user_id')->references('id')->on('company_users');
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
        Schema::dropIfExists('signature_containers');
    }
}
