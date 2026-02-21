<?php

use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessTable extends Migration
{
    use OwnDatabase;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('school_year_id');
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->text('message_footer')->nullable();
            $table->text('grade_age_range_link')->nullable();
            $table->timestamps();
        });

        if ($this->useThirdPartyDatabase()) {
            return;
        }

        Schema::table('processes', function (Blueprint $table) {
            $table->foreign('school_year_id')->references('id')->on('school_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processes');
    }
}
