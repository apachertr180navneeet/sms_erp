<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('student_limit')->default(0)->after('price');
            $table->integer('staff_limit')->default(0)->after('student_limit');
            $table->integer('storage_limit_mb')->default(1024)->after('staff_limit');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['student_limit', 'staff_limit', 'storage_limit_mb']);
        });
    }
};
