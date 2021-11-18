<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company');
            $table->foreign('company')->references('id')->on('companies');
            $table->string('code');
            $table->string('title');
            $table->string('description');
            $table->string('email_title');
            $table->string('email');
            $table->string('telephone_title');
            $table->string('telephone');
            $table->string('country');
            $table->string('city');
            $table->string('address');
            $table->string('logo');
            $table->dateTime('started_at', $precision = 0);
            $table->dateTime('ended_at', $precision = 0);
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
        Schema::dropIfExists('projects');
    }
}
