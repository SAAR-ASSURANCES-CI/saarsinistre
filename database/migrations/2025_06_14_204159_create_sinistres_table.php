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
            $table->string('numero_sinistre', 50)->unique();

            $table->string('nom_assure');
            $table->string('email_assure');
            $table->string('telephone_assure', 20);
            $table->string('numero_police', 50);

            $table->date('date_sinistre');
            $table->string('lieu_sinistre', 500);
            $table->text('circonstances');
            $table->string('conducteur_nom');

            $table->boolean('constat_autorite')->default(false);
            $table->string('officier_nom')->nullable();
            $table->string('commissariat')->nullable();
            $table->text('dommages_releves')->nullable();

            $table->enum('statut', [
                'en_attente',
                'en_cours',
                'expertise_requise',
                'en_attente_documents',
                'pret_reglement',
                'regle',
                'refuse',
                'clos'
            ])->default('en_attente');

            $table->foreignId('gestionnaire_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_affectation')->nullable();
            $table->timestamp('date_reglement')->nullable();
            $table->decimal('montant_estime', 12, 2)->nullable();
            $table->decimal('montant_regle', 12, 2)->nullable();

            $table->integer('jours_en_cours')->default(0);
            $table->boolean('en_retard')->default(false);
            $table->timestamp('derniere_notification')->nullable();

            $table->timestamps();

            $table->index(['statut', 'gestionnaire_id']);
            $table->index(['date_sinistre', 'statut']);
            $table->index(['en_retard', 'statut']);
            $table->index('numero_police');
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
