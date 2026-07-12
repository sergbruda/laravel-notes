<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('notes', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            $t->charset = 'utf8mb4';
            $t->id();
            $t->foreignId('user_id')->constrained()->onDelete('cascade');
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->text('content');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('notes');
    }
};
