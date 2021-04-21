<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCryptocardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cryptocards', function (Blueprint $table) {
            $table->increments('id');

            $table->text('code')->nullable();

            $table->float('BTC', 8, 4);

            $table->float('BTC_EUR');

            $table->float('EUR');

            $table->datetime('rateTimestamp');

            $table->datetime('activatedFrom');

            $table->integer('status');

            $table->integer('activatedBy');

            $table->integer('assignedToUser')->unsigned()->index()->nullable();
            $table->foreign('assignedToUser')->references('id')->on('users');

            $table->integer('wallet_id')->unsigned()->nullable(); //new
            $table->foreign('wallet_id')->references('id')->on('wallets');   
            
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
        Schema::dropIfExists('cryptocards');
    }
}
