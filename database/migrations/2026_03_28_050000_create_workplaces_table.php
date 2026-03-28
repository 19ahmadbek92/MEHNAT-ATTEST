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
        Schema::create('workplaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Kasb yoki ish o'rni nomi
            $table->string('code')->nullable(); // OKZ yoki boshqa kod
            $table->string('department')->nullable(); // Sex, bo'lim nomi
            $table->enum('status', ['pending', 'in_progress', 'attested', 'rejected'])->default('pending');
            $table->integer('employees_count')->default(1);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
        * Reverse the migrations.
        */
    public function down(): void
    {
        Schema::dropIfExists('workplaces');
    }
};
