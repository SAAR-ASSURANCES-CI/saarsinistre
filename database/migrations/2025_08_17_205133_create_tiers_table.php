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
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sinistre_id');
            $table->integer('numero_tiers');
            
            $table->string('marque_vehicule')->nullable();
            $table->string('modele_vehicule')->nullable();
            $table->string('immatriculation')->nullable();
            
            $table->string('nom_conducteur')->nullable();
            $table->string('prenom_conducteur')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            
            $table->string('compagnie_assurance')->nullable();
            $table->string('numero_police_assurance')->nullable();
            $table->text('details_assurance')->nullable();
            
            $table->text('details_supplementaires')->nullable();
            
            $table->timestamps();
            
            $table->foreign('sinistre_id')->references('id')->on('sinistres')->onDelete('cascade');
            
            $table->index(['sinistre_id', 'numero_tiers']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
