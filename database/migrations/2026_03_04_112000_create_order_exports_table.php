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
        Schema::create('order_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status', 32)->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('exported_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_exports');
    }
};
