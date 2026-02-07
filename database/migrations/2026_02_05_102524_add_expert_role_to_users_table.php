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
        // Le rôle 'expert' est déjà inclus dans la migration originale (0001_01_01_000000_create_users_table)
        // Cette migration est conservée pour l'historique mais ne fait rien
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à faire
    }
};
