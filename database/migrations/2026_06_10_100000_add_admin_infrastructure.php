<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE users ALTER COLUMN role DROP DEFAULT');
        DB::statement('ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(20) USING role::text');
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'user'");

        Schema::table('claims', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('rejection_reason')->nullable()->after('reviewed_at');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->default('system')->after('id');
            $table->string('title')->nullable()->after('type');
            $table->string('related_type')->nullable()->after('message');
            $table->unsignedBigInteger('related_id')->nullable()->after('related_type');
            $table->timestamp('read_at')->nullable()->after('is_read');
        });

        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'admin'))");
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'title', 'related_type', 'related_id', 'read_at']);
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['reviewed_by', 'reviewed_at', 'rejection_reason']);
        });

    }
};
