<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            
            $table->renameColumn('category_id', 'categorie_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            
            $table->renameColumn('categorie_id', 'category_id');
        });
    }
};
