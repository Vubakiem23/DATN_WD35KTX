<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // varchar(255)
            $table->unsignedBigInteger('type_id');
            $table->timestamp('read_at')->nullable();

            // Unique constraint (user_id + type + type_id)
            $table->unique(['user_id', 'type', 'type_id'], 'notification_reads_user_id_type_type_id_unique');

            // Indexes
            $table->index('user_id');
            $table->index('read_at');

            // Foreign key
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
    }
};
