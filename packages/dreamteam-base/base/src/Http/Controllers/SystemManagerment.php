<?php

namespace DreamTeam\Base\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use DreamTeam\Base\Models\Setting;
use DreamTeam\Base\Events\ClearCacheEvent;

class SystemManagerment extends AdminController
{
    public function updateLicense(Request $request)
    {
        try {
            $settingName = 'theme_validate';
            $data = $request->all();
            $unset = ['_token', 'redirect', 'setLanguage'];
            foreach ($unset as $value) {
                unset($data[$value]);
            }
            $data = removeScriptArray($data);
            $data = base64_encode(json_encode($data));
            if (Setting::where('key', $settingName)->exists()) {
                Setting::where('key', $settingName)->update([
                    'value'     => $data
                ]);
            } else {
                Setting::insert([
                    'key'       => $settingName,
                    'locale'    => '',
                    'value'     => $data
                ]);
            }
            event(new ClearCacheEvent());
            Artisan::call('dreamteam:clear');
            return response()->json(['error' => false]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }
    }
}
