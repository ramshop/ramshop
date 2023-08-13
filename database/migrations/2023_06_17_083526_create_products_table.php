<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code", 5)->nullable();
            $table->foreignId("category_id")->nullable()->noActionOnDelete();
            $table->decimal("price")->default(0);
            $table->decimal("old_price")->nullable();
            $table->decimal('cost')->nullable();
            $table->string("unit")->default("");
            $table->string("image")->nullable();
            $table->string("description")->nullable();
            $table->boolean("is_visible")->default(true);
            $table->string("remarks", 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("products");
    }
};
