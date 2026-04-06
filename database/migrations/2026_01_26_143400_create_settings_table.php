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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, boolean, json, image
            $table->string('group')->default('general'); // general, site, payment, shipping, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // whether setting is accessible on frontend
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['group', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
