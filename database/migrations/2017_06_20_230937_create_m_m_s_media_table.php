<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMMSMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_m_s_media', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('mediaSid')->index();
            $table->string('MessageSid')->index();
            $table->string('mediaUrl')->index();
            $table->binary('media');
            $table->string('filename')->index();
            $table->string('MIMEType');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_m_s_media');
    }
}
