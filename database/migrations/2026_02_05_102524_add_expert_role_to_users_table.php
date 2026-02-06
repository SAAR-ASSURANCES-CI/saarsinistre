<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour ajouter 'expert'
        // Note: En MySQL, on doit modifier toute la colonne enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'gestionnaire', 'assure', 'expert') DEFAULT 'assure'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer 'expert' de l'enum (remettre l'ancien enum)
        // Attention: Cette opération peut échouer si des utilisateurs avec le rôle 'expert' existent
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'gestionnaire', 'assure') DEFAULT 'assure'");
    }
};
