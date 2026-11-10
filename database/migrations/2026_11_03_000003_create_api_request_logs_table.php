<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_client_id')->nullable()->constrained('api_clients')->nullOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->nullOnDelete();
            $table->string('endpoint', 255);
            $table->string('method', 10);
            $table->unsignedSmallInteger('status_code');
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->unsignedInteger('rate_limit_limit')->nullable();
            $table->unsignedInteger('rate_limit_remaining')->nullable();
            $table->boolean('was_blocked')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamps();

            $table->index('api_client_id');
            $table->index('requested_at');
            $table->index('endpoint');
            $table->index('method');
            $table->index('status_code');
            $table->index('was_blocked');
            $table->index(['api_client_id', 'requested_at']);
            $table->index(['was_blocked', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};
