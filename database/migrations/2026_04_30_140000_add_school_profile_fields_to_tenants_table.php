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
            $table->string('school_logo')->nullable()->after('name');
            $table->text('school_address')->nullable()->after('school_logo');
            $table->string('school_phone')->nullable()->after('school_address');
            $table->string('school_email')->nullable()->after('school_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'school_logo',
                'school_address',
                'school_phone',
                'school_email',
            ]);
        });
    }
};
