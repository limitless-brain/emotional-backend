<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('album_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('artist_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->integer('duration_ms');
            $table->integer('track_number');
            $table->integer('disc_number')->default(1);
            $table->text('lyrics')->nullable();
            $table->text('path')->nullable();
            $table->string('youtube_id')->nullable();
            $table->string('feeling')->default('neutral');
            $table->integer('year');
            $table->timestamp('release_date');
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
        Schema::dropIfExists('songs');
    }
}
