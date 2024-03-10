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
        Schema::create('waypoint_distances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin_id');
            $table->unsignedBigInteger('destination_id');
            $table->float('distance');
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('origin_id')->references('id')->on('waypoints');
            $table->foreign('destination_id')->references('id')->on('waypoints');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waypoint_distances');
    }
};
