<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Base\Enums\BaseStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->text('summary')->after('avatar')->nullable();
            $table->tinyInteger('enabel_google2fa')->after('google2fa_secret')->default(BaseStatusEnum::DEACTIVE);
        });
        $users = DB::table('admin_users')->select('id', 'infomation')->get();
        if (count($users)) {
            foreach($users as $user) {
                DB::table('admin_users')->where('id', $user->id)->update(['summary' => cutString(removeHTML($user->infomation), 150)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn('summary');
            $table->dropColumn('enabel_google2fa');
        });
    }
};
