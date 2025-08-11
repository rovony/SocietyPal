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
        Schema::create('society_forum_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('society_id')->nullable();
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name'); 
            $table->text('icon')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();
        });

        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('society_id')->nullable();
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('society_forum_category')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('discussion_type', ['private', 'public']);
            $table->string('user_selection_type')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->timestamps();

        });

        Schema::create('forum_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_id');
            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');
            $table->string('file'); 
            $table->timestamps();
        });

        Schema::create('forum_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_id')->nullable();
            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_id')->nullable();
            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('parent_reply_id')->nullable();
            $table->foreign('parent_reply_id')->references('id')->on('forum_replies')->onDelete('cascade')->onUpdate('cascade');
            $table->text('reply');
            $table->timestamps();
        });

        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_id')->nullable();
            $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        
            $table->unique(['forum_id', 'user_id']); // Ensure a user can only like once
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('society_forum_category');
        Schema::dropIfExists('forums');
        Schema::dropIfExists('forum_files');
        Schema::dropIfExists('forum_user');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_likes');
    }
};
