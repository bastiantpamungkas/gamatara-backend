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
        Schema::create('attendance_guests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('guest_id');
            $table->date('date');
            $table->time('time_check_in');
            $table->time('time_check_out')->nullable();
            $table->text('need');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_guests');
    }
};
