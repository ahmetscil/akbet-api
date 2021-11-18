<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownlinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downlink', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('measurement')->nullable();
            $table->foreign('measurement')->references('id')->on('measurement');
            $table->string('DevEUI');
            $table->longText('payload_hex');
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
        Schema::dropIfExists('downlink');
    }
}
