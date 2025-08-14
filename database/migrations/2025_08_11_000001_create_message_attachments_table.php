<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->string('type_mime', 100);
            $table->unsignedBigInteger('taille');
            $table->timestamps();

            $table->index(['message_id']);
            $table->index(['type_mime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};


