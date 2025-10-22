<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });

        DB::table('order_status')->insert([
            ['status' => 'Solicitado',  'created_at' => now(), 'updated_at' => now()],
            ['status' => 'Aprovado',  'created_at' => now(), 'updated_at' => now()],
            ['status' => 'Cancelado', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->string('destination');
            $table->dateTime('departure_date');
            $table->dateTime('return_date');
            $table->biginteger('order_status_id')->unsigned()->default(1);
            $table->biginteger('user_id')->unsigned();
            $table->foreign('order_status_id')->references('id')->on('order_status')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
        Schema::dropIfExists('order_status');
    }
};
