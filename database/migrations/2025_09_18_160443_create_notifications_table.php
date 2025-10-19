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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Which user receives the notification
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Optional: Which issue triggered this notification
            $table->foreignId('issue_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('type'); // e.g., 'issue_assigned', 'status_updated'
            $table->text('message'); // Notification message

            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
