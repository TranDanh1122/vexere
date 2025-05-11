<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Media\Facades\RvMedia;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $folders = ['uploads', 'clients'];
        $folderId = 0;
        foreach($folders as $folder) {
            $folderId = RvMedia::createFolder($folder, $folderId);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
