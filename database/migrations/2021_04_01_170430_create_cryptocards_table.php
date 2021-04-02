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

            $table->float('BTC');

            $table->float('BTC_EUR');

            $table->float('EUR');

            $table->datetime('rateTimestamp');

            $table->datetime('activatedFrom');

            $table->integer('status');

            $table->integer('activatedBy');

            $table->integer('assignedToUser');

            $table->integer('wallet_id')->unsigned()->index(); //new
            $table->foreign('wallet_id')->references('id')->on('wallets')->onUpdate('cascade')->onDelete('cascade');     

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
