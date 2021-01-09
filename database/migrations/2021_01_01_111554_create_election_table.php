<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('election', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('ballot_model');
            $table->text('voters');
            $table->text('managers');
            $table->timestamp('deadline');
            $table->text('vote_email');
            $table->text('voted_email');
            $table->text('email_sender');
            $table->text('not_voted_email');
            $table->text('public_key');
            $table->text('private_key');
            $table->text('counters');
            $table->string('closed', 1);
            $table->string('is_active');
            $table->string('created_by');
            $table->string('modified_by');
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
        Schema::dropIfExists('election');
    }
}
