<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBallotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ballot', function (Blueprint $table) {
            $table->id();
            $table->longText('election_id');
            $table->longText('ballot_content');
            $table->string('assigned');
            $table->string('voted', 1);
            $table->timestamp('voted_on')->nullable()->default(null);
            $table->text('result');
            $table->longText('ballot_uuid');
            $table->longText('signature');
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
        Schema::dropIfExists('ballot');
    }
}
