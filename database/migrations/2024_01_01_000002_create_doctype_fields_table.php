<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctype_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctype_id')->constrained()->cascadeOnDelete();
            $table->string('fieldname');
            $table->string('label');
            $table->string('fieldtype'); // text, select, number, date, etc.
            $table->json('options')->nullable(); // For select fields, validation rules, etc.
            $table->boolean('required')->default(false);
            $table->boolean('unique')->default(false);
            $table->boolean('in_list_view')->default(false);
            $table->boolean('in_standard_filter')->default(false);
            $table->text('description')->nullable();
            $table->text('default_value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['doctype_id', 'fieldname']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctype_fields');
    }
};
