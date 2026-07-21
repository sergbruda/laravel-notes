<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'xp')) {
                $table->unsignedInteger('xp')->default(0);
                $table->unsignedInteger('streak')->default(0);
            }
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('unlocked_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'streak']);
        });
    }
};