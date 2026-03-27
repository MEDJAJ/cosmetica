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
    Schema::table('orders', function (Blueprint $table) {
        
        $table->enum('status', ['en attente', 'en préparation', 'livrée', 'annulée'])
              ->default('en attente')
              ->change();
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->enum('status', ['en attente', 'en préparation', 'livrée'])
              ->default('en attente')
              ->change();
    });
}
};
