<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('audited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('audit_batch_id')->index()->nullable();
            $table->date('audit_date')->index();
            $table->enum('status', ['verified', 'need_repair', 'missing'])->default('verified')->index();
            $table->string('condition')->nullable();
            $table->boolean('location_match')->default(true);
            $table->text('findings')->nullable();
            $table->string('evidence_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_records');
    }
};
