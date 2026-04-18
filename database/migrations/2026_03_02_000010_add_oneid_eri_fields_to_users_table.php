<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pinfl', 14)->nullable()->unique()->after('email');
            $table->string('tin', 20)->nullable()->unique()->after('pinfl');
            $table->enum('person_type', ['jismoniy', 'yuridik'])->nullable()->after('tin');
            $table->boolean('is_verified')->default(false)->after('person_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pinfl', 'tin', 'person_type', 'is_verified']);
        });
    }
};
