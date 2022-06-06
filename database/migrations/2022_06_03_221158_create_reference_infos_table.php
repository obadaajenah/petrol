<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferenceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reference_infos', function (Blueprint $table) {

            //yujhjj
            $table->id();
            $table->string('car_number')->unique();
            $table->string('owner');
            $table->string('type');
            $table->string('category');
            $table->date('manufacturing_year');
            $table->string('engine_number');
            $table->string('passengers_number');
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
        Schema::dropIfExists('reference_infos');
    }
}
