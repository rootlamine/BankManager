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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prenom');
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('numero_telephone')->nullable();
            $table->string('mot_de_passe');
            $table->text('adresse')->nullable();
            $table->enum('role', ['client', 'admin'])->default('client');
            $table->string('niveau_admin')->nullable(); // Pour les admins
            $table->timestamp('date_inscription')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
