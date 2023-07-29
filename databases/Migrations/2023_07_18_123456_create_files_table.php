<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->morphs('fileable');
            $table->string('path')->unique();
            $table->string('name');
            $table->string('type')->nullable(); // Novo campo: tipo do arquivo
            $table->unsignedBigInteger('size')->nullable(); // Novo campo: tamanho do arquivo em bytes
            $table->string('mime_type')->nullable(); // Novo campo: MIME type do arquivo
            $table->text('description')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}