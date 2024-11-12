<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreignId('sub_category_id')
                ->references('id')
                ->on('sub_categories')
                ->onDelete('cascade');
            $table->foreignId('location_id')
                ->references('id')
                ->on('locations')
                ->onDelete('cascade');
            $table->string('product_name');
            $table->text('description');
            $table->string('duration_of_use');
            $table->string('phone_number');
            $table->boolean('product_status')->default(false);
            $table->boolean('is_sold')->default(false);
            $table->integer('items_count');
            $table->boolean('is_free');
            $table->boolean('is_deliverable');
            $table->boolean('price_suggestion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
