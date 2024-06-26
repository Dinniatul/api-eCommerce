<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->onDelete('cascade');
            $table->text('cart_id')->onDelete('cascade');
            // $table->integer('quantity');
            $table->decimal('totalPayment', 10, 3);
            $table->enum('status', ['Belum Dibayar', 'Sudah Dibayar', 'Selesai']);
            $table->enum('methodPayment', ['Cash']);
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
        Schema::dropIfExists('orders');
    }
}
