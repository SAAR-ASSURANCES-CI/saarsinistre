<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_tiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tiers_id');
            $table->string('type_document'); // 'photo_vehicule', 'attestation_assurance', etc.
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->integer('taille_fichier')->nullable();
            $table->string('extension', 10)->nullable();
            $table->timestamps();
            
            $table->foreign('tiers_id')->references('id')->on('tiers')->onDelete('cascade');
            $table->index(['tiers_id', 'type_document']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_tiers');
    }
};
