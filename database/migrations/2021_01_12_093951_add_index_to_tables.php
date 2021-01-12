<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->index(['title','feeling','youtube_id','year','release_date']);
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->index(['name']);
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->index(['name']);
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
