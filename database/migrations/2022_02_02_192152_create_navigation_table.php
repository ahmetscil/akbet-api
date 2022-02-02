<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company');
            $table->foreign('company')->references('id')->on('companies');
            $table->string('type')->default('web');
            $table->string('title');
            $table->string('alt')->nullable();
            $table->string('route')->nullable();
            $table->string('params')->nullable();
            $table->integer('order')->default(1)->nullable();
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
        Schema::dropIfExists('navigation');
    }
}
