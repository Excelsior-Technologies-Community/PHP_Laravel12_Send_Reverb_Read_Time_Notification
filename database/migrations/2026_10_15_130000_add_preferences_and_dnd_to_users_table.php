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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_notifications')->default(true)->after('remember_token');
            $table->boolean('sound_notifications')->default(true)->after('email_notifications');
            $table->boolean('dnd_enabled')->default(false)->after('sound_notifications');
            $table->string('dnd_start_time', 5)->default('22:00')->after('dnd_enabled');
            $table->string('dnd_end_time', 5)->default('08:00')->after('dnd_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_notifications',
                'sound_notifications',
                'dnd_enabled',
                'dnd_start_time',
                'dnd_end_time',
            ]);
        });
    }
};
