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
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('society_id')->nullable();
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('event_name');
            $table->string('where');
            $table->mediumText('description');
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('assign_to', ['role', 'user'])->default('role');
            $table->timestamps();
        });

        Schema::create('event_attendees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->unsignedInteger('event_id')->nullable();
            $table->foreign(['event_id'])->references(['id'])->on('events')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->timestamps();
        });

        Schema::create('event_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('event_role');
    }
};
