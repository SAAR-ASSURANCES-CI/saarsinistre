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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_id')->constrained()->onDelete('cascade');
            
            $table->string('marque');
            $table->string('immatriculation')->unique();
            $table->string('numero_chassis');
            $table->string('modele')->nullable();
            $table->year('annee')->nullable();
            $table->string('couleur')->nullable();
            $table->enum('type', ['voiture', 'moto', 'camion', 'utilitaire', 'autre'])->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
