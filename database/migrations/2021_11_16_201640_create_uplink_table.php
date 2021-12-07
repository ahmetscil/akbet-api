<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUplinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uplink', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('measurement')->nullable();
            $table->foreign('measurement')->references('id')->on('measurement');
            $table->string('DevEUI');
            $table->longText('payload_hex');
            $table->string('LrrRSSI');
            $table->string('LrrSNR');
            $table->string('temperature');
            $table->string('maturity');
            $table->string('strength')->nullable();
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
        Schema::dropIfExists('uplink');
    }
}
