<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable();
            $table->string('request_received')->nullable();
            $table->string('stage_model')->default('new');
            $table->string('pickup')->nullable();
            $table->string('dropoff')->nullable();
            $table->string('category')->nullable();
            $table->string('size')->nullable();
            $table->string('insurance')->nullable();
            $table->boolean('finished')->default(false);
            $table->integer('session')->default(400);
            $table->boolean('terminate')->default(false);
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
        Schema::dropIfExists('estimates');
    }
}
