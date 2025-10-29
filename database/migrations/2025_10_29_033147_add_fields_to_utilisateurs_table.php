<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->string('nci')->unique()->nullable()->after('email');
            $table->string('code_verification')->nullable()->after('mot_de_passe');
            $table->boolean('premiere_connexion')->default(true)->after('code_verification');
        });
    }

    public function down()
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn(['nci', 'code_verification', 'premiere_connexion']);
        });
    }
};
