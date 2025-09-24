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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // success, error, warning, info
            $table->string('message');
            $table->string('title')->nullable();
            $table->string('user_id')->nullable(); // For user-specific alerts
            $table->string('session_id')->nullable(); // For session-based alerts
            $table->string('alert_type')->default('alert'); // alert, toast, modal, inline
            $table->string('theme')->default('bootstrap'); // bootstrap, tailwind, bulma
            $table->string('position')->default('top-right'); // For toasts
            $table->string('animation')->default('fade'); // fade, slide, bounce, etc.
            $table->boolean('dismissible')->default(true);
            $table->boolean('auto_dismiss')->default(false);
            $table->integer('auto_dismiss_delay')->nullable(); // in milliseconds
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->json('options')->nullable(); // Additional options
            $table->json('data_attributes')->nullable(); // Custom data attributes
            $table->string('icon')->nullable(); // Custom icon
            $table->text('html_content')->nullable(); // HTML content
            $table->string('class')->nullable(); // Custom CSS classes
            $table->text('style')->nullable(); // Custom styles
            $table->string('context')->nullable(); // For inline alerts
            $table->string('field')->nullable(); // For field-specific alerts
            $table->string('form')->nullable(); // For form-specific alerts
            $table->integer('priority')->default(0); // Alert priority
            $table->boolean('is_active')->default(true); // Whether alert is active
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['session_id', 'is_active']);
            $table->index(['type', 'is_active']);
            $table->index(['alert_type', 'is_active']);
            $table->index(['expires_at']);
            $table->index(['dismissed_at']);
            $table->index(['read_at']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
