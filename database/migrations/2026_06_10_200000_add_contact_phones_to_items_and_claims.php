<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('contact_phone', 20)->nullable()->after('user_id');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->string('contact_phone', 20)->nullable()->after('claim_notes');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('contact_phone');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('contact_phone');
        });
    }
};
