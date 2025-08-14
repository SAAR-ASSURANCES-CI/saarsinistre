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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinistre_id')->constrained('sinistres')->onDelete('cascade');
            $table->foreignId('assure_id')->constrained('users')->onDelete('cascade');
            
            $table->integer('note_service')->nullable()->comment('Note de 1 à 5');
            $table->string('humeur_emoticon', 10)->nullable()->comment('Emoticon représentant l\'humeur');
            $table->text('commentaire')->nullable()->comment('Commentaire libre de l\'assuré');
            
            $table->timestamp('date_feedback')->nullable();
            $table->boolean('envoye_automatiquement')->default(false);
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['sinistre_id', 'assure_id']);
            $table->index('date_feedback');
            $table->index('note_service');
            $table->index('humeur_emoticon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
