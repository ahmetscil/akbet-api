<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasurementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measurement', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('mix')->nullable();
            $table->foreign('mix')->references('id')->on('mix');
            $table->unsignedBigInteger('sensor')->nullable();
            $table->foreign('sensor')->references('id')->on('sensors');
            $table->string('max_temp');
            $table->string('min_temp');
            $table->string('last_temp');
            $table->string('readed_max');
            $table->string('readed_min');
            $table->dateTime('started_at', $precision = 0);
            $table->dateTime('ended_at', $precision = 0);
            $table->dateTime('deployed_at', $precision = 0);
            $table->dateTime('last_data_at', $precision = 0);
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
        Schema::dropIfExists('measurement');
    }
}
