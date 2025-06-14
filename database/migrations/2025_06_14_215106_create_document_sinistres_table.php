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
            $table->foreignId('sinistre_id')->constrained('sinistres')->onDelete('cascade');

            $table->enum('type_document', [
                'carte_grise_recto',
                'carte_grise_verso',
                'visite_technique_recto',
                'visite_technique_verso',
                'attestation_assurance',
                'permis_conduire',
                'photo_vehicule',
                'constat_amiable',
                'facture_reparation',
                'rapport_expert',
                'autres'
            ]);

            $table->string('libelle_document');
            $table->string('nom_fichier');
            $table->string('nom_fichier_stocke');
            $table->string('chemin_fichier');
            $table->string('type_mime', 100);
            $table->unsignedBigInteger('taille_fichier');

            $table->enum('statut_verification', ['en_attente', 'verifie', 'rejete'])
                ->default('en_attente');
            $table->text('commentaire_verification')->nullable();
            $table->foreignId('verifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verifie_le')->nullable();

            $table->timestamps();

            $table->index(['sinistre_id', 'type_document']);
            $table->index('statut_verification');
            $table->index(['type_document', 'statut_verification']);
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
