<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMixCalibrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mix_calibration', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mix')->nullable();
            $table->foreign('mix')->references('id')->on('mix');
            $table->string('days');
            $table->string('strength');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('mix_calibration');
    }
}
