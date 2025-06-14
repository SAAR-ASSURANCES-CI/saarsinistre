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
        Schema::create('sinistres', function (Blueprint $table) {
            $table->id();
            $table->string('numero_sinistre')->unique();

            $table->string('nom_assure')->nullable();
            $table->string('email_assure')->nullable();
            $table->string('telephone_assure')->nullable();
            $table->string('numero_police')->nullable();

            $table->date('date_sinistre')->nullable();
            $table->string('lieu_sinistre')->nullable();
            $table->text('circonstances')->nullable();
            $table->string('conducteur_nom')->nullable();
            $table->boolean('constat_autorite')->default(false);
            $table->string('officier_nom')->nullable();
            $table->string('commissariat')->nullable();
            $table->text('dommages_releves')->nullable();

            $table->enum('statut', [
                'nouveau',
                'en_cours',
                'expertise_requise',
                'en_attente_documents',
                'pret_reglement',
                'regle',
                'refuse',
                'clos'
            ])->default('nouveau');

            $table->foreignId('gestionnaire_id')->nullable()->constrained('users');
            $table->timestamp('date_affectation')->nullable();
            $table->timestamp('date_reglement')->nullable();
            $table->decimal('montant_estime', 12, 2)->nullable();
            $table->decimal('montant_regle', 12, 2)->nullable();

            // Suivi temporel
            $table->integer('jours_en_cours')->default(0);
            $table->boolean('en_retard')->default(false);
            $table->timestamp('derniere_notification')->nullable();

            $table->timestamps();

            $table->index(['statut', 'gestionnaire_id']);
            $table->index(['date_sinistre', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinistres');
    }
};
