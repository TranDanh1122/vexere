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
        Schema::table('seos', function (Blueprint $table) {
            $table->tinyInteger('is_custom_canonical')->after('social_description')->default(0);
            $table->text('canonical')->after('is_custom_canonical')->nullable();
            $table->tinyInteger('show_on_sitemap')->after('canonical')->default(1)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seos', function (Blueprint $table) {
            $table->dropColumn('is_custom_canonical');
            $table->dropColumn('canonical');
            $table->dropColumn('show_on_sitemap');
        });
    }
};
