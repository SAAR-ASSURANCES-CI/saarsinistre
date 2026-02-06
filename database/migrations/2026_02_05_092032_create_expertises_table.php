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
        Schema::create('expertises', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('sinistre_id')->constrained('sinistres')->onDelete('cascade');
            $table->foreignId('expert_id')->constrained('users')->onDelete('cascade');
            
            // Informations générales
            $table->date('date_expertise');
            $table->string('client_nom');
            
            // Informations collaborateur
            $table->string('collaborateur_nom');
            $table->string('collaborateur_telephone', 50); // Format: "0747707127/0711236714"
            $table->string('collaborateur_email');
            
            // Informations expertise
            $table->string('lieu_expertise'); // Commune
            $table->string('contact_client', 20)->nullable();
            
            // Tableau d'opérations (stocké en JSON)
            $table->json('operations');
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('sinistre_id');
            $table->index('expert_id');
            $table->index('date_expertise');
            
            // Contrainte unique : une seule expertise par sinistre
            $table->unique('sinistre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expertises');
    }
};
