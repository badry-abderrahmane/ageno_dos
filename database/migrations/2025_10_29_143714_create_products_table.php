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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('ref')->unique();
            $table->string('delivery_time')->nullable()->default('0');
            $table->string('supplier_price')->nullable()->default('0');
            $table->string('price')->nullable()->default('0');
            $table->string('min_qty')->nullable()->default('0');
            $table->string('max_qty')->nullable()->default('0');
            $table->string('note')->nullable();
            $table->string('img')->nullable();
            $table->integer('product_category_id')->unsigned();
            $table->integer('supplier_id')->unsigned();

            $table->foreign('product_category_id')
                ->references('id')->on('product_categories')
                ->onDelete('cascade');

            $table->foreign('supplier_id')
                ->references('id')->on('suppliers')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
