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
        Schema::create('document_sinistres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_id')->constrained()->onDelete('cascade');
            $table->enum('type_document', [
                'carte_grise_recto',
                'carte_grise_verso',
                'visite_technique_recto',
                'visite_technique_verso',
                'attestation_assurance',
                'permis_conduire',
                'photo_vehicule',
                'autres'
            ]);
            $table->string('nom_fichier')->nullable();
            $table->string('chemin_fichier')->nullable();
            $table->string('type_mime')->nullable();
            $table->integer('taille_fichier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sinistres');
    }
};
