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
        Schema::create('product_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->string('direction')->comment('Hướng đi'); // Enum
            $table->tinyInteger('monday')->default(1); // Ngày đi T2-CN
            $table->tinyInteger('tuesday')->default(1);
            $table->tinyInteger('wednesday')->default(1);
            $table->tinyInteger('thursday')->default(1);
            $table->tinyInteger('friday')->default(1);
            $table->tinyInteger('saturday')->default(1);
            $table->tinyInteger('sunday')->default(1);
            $table->time('time')->comment('Thời gian đi')->nullable();
            $table->string('time_run')->comment('Tổng Thời gian chạy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_schedules');
    }
};
