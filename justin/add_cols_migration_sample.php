<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('new_data')->nullable();      // string column
            $table->json('extra_data')->nullable();      // JSON column
            $table->longText('content')->nullable();     // Longtext column
            $table->integer('views')->default(0);        // Number (integer)
            $table->timestamp('published_at')->nullable(); // Timestamp column
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['extra_data', 'content', 'views', 'published_at']);
        });
    }
};
?>