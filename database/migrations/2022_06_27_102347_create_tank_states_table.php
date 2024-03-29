<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTankStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tank_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gasStation_id');
            $table->integer('amount')->max(20000);
            $table->foreignId('refill_id');
            $table->date('start_full');
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
        Schema::dropIfExists('tank_states');
    }
}
