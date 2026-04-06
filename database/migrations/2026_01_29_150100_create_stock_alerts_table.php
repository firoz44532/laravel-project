<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('threshold_quantity')->default(10);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sent')->default(false);
            $table->timestamp('last_sent_at')->nullable();
            $table->string('alert_type')->default('email'); // email, sms, dashboard
            $table->timestamps();
            
            $table->unique(['product_id']);
            $table->index(['is_active', 'is_sent']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_alerts');
    }
};
