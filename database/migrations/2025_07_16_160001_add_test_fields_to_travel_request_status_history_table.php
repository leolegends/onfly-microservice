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
        Schema::table('travel_request_status_history', function (Blueprint $table) {
            // Adicionar os campos que os testes esperam
            $table->string('status')->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_request_status_history', function (Blueprint $table) {
            $table->dropForeign(['changed_by']);
            $table->dropColumn(['status', 'comment', 'changed_by']);
        });
    }
};
