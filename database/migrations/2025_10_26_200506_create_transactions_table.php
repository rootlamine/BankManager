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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['depot', 'retrait', 'virement']);
            $table->decimal('montant', 15, 2);
            $table->enum('status', ['complet', 'echec', 'attente'])->default('attente');
            $table->text('description')->nullable();
            $table->string('numero_compte_source');
            $table->string('numero_compte_destination')->nullable();
            $table->foreignUuid('compte_id')->constrained('comptes')->onDelete('cascade');
            $table->timestamp('date_transaction')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
