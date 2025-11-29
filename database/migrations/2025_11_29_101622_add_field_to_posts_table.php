<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void{
        Schema::table('posts', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('serial', 100)->nullable();
            $table->string('country',4)->nullable()->default('N/A');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('country',4)->nullable()->default('N/A');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void{
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('serial');
            $table->dropColumn('country');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
