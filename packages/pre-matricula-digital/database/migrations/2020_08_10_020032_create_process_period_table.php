<?php

use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessPeriodTable extends Migration
{
    use OwnDatabase;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_period', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('process_id');
            $table->integer('period_id');
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
        });

        if ($this->useThirdPartyDatabase()) {
            return;
        }

        Schema::table('process_period', function (Blueprint $table) {
            $table->foreign('period_id')->references('id')->on('periods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_period');
    }
}
