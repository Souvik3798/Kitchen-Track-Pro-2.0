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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('inventories')->onDelete('cascade');
            $table->foreignId('shopstock_id')->constrained('shop_stocks')->onDelete('cascade');
            $table->decimal('quantity', 10, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
