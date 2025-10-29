<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comptes', function (Blueprint $table) {
            $table->text('motif_blocage')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('comptes', function (Blueprint $table) {
            $table->dropColumn('motif_blocage');
        });
    }
};
