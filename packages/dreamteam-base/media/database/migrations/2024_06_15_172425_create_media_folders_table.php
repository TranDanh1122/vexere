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
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('name')->nullable();
            $table->string('color')->nullable();
            $table->string('slug')->nullable();
            $table->foreignId('parent_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['parent_id', 'user_id', 'created_at'], 'media_folders_index');
        });

        Schema::table('medias', function (Blueprint $table) {
            $table->foreignId('folder_id')->default(0);
            $table->string('mime_type', 120);
            $table->text('options')->nullable();
            $table->softDeletes();
            $table->index(['folder_id', 'user_id', 'created_at'], 'media_files_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_folders');
        
        Schema::table('medias', function (Blueprint $table) {
            $table->dropColumn('folder_id');
            $table->dropColumn('options');
            $table->dropSoftDeletes();
            $table->dropIndex('media_files_index');
        });
    }
};
