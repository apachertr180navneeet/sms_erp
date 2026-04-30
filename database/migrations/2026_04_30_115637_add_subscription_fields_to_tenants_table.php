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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->boolean('subscription_active')->default(false)->after('name');
            $table->timestamp('subscription_starts_at')->nullable()->after('subscription_active');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_starts_at');
            $table->decimal('subscription_amount', 10, 2)->nullable()->after('subscription_ends_at');
            $table->foreignId('subscription_package_id')->nullable()->after('subscription_amount')->constrained('subscription_packages');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['name', 'subscription_active', 'subscription_starts_at', 'subscription_ends_at', 'subscription_amount', 'subscription_package_id']);
        });
    }
};
