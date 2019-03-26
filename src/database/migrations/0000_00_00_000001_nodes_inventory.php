<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NodesInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_bridge_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('agency_id')->unsigned();
            if(config('business.product_variations')){
                $table->integer('product_bridge_variation_id')->nullable();
            }
            $table->integer('initial_quantity')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('product_bridges')->onDelete('cascade');
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
        });
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agency_id')->unsigned();
            $table->integer('user_id')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->enum('type', ['normal', 'online'])->nullable()->default('normal');
            $table->string('name')->nullable();
            $table->text('files')->nullable();
            $table->enum('status', ['pending','delivered','paid'])->nullable()->default('pending');
            $table->timestamps();
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('product_bridge_id')->unsigned();
            $table->enum('status', ['holding','finished'])->nullable()->default('holding');
            $table->integer('initial_quantity')->nullable()->default(0);
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('currency_id')->unsigned();
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('product_bridge_id')->references('id')->on('product_bridges')->onDelete('cascade');
        });
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_bridge_id')->unsigned();
            $table->integer('agency_id')->unsigned();
            $table->string('name')->nullable();
            $table->enum('type', ['move_in','move_out'])->nullable()->default('move_in');
            $table->integer('quantity')->nullable();
            $table->timestamps();
            $table->foreign('product_bridge_id')->references('id')->on('product_bridges')->onDelete('cascade');
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('purchase_products');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('product_bridge_stocks');
    }
}
