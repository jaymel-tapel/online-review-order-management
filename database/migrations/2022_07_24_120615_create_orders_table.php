<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->timestamps();
            $table->integer('company_id');
            $table->integer('seller_id');
            $table->integer('client_id');
            $table->integer('number_of_reviews');
            $table->decimal('unit_cost', 5,2);
            $table->decimal('total_price', 5,2);
            $table->string('reviewers');
            $table->string('remarks')->nullable();
            $table->integer('order_status')
            ->default('0')
            ->comment('0 - New, 1 - Seen, 2 - Preparing, 3 - Finished');;
            $table->string('payment_status')
            ->default('0')
            ->comment('0 - New, 1 - Sent Invoice, 2 - Paid');
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
};
