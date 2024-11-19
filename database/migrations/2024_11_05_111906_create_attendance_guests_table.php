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
            $table->string('institution')->nullable();
            $table->datetime('time_check_in');
            $table->datetime('time_check_out')->nullable();
            $table->string('type_vehicle')->nullable();
            $table->string('no_police')->nullable();
            $table->bigInteger('total_guest');
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
