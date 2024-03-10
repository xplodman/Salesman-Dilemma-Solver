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
        Schema::table('journey_attempts', function (Blueprint $table) {
            $table->unsignedBigInteger('start_waypoint_id');
            $table->foreign('start_waypoint_id')->references('id')->on('waypoints')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journey_attempts', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['start_waypoint_id']);

            // Remove the start_waypoint_id column
            $table->dropColumn('start_waypoint_id');
        });
    }
};
