<?php

namespace DreamTeam\AdminUser\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DreamTeam\AdminUser\Http\Resources\UserResource;
use DreamTeam\Base\Http\Responses\BaseHttpResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DreamTeam\Media\Facades\RvMedia;

class ProfileController extends Controller
{
    /**
     * Get the user profile information.
     *
     * @group Profile
     * @authenticated
     */
    public function getProfile(Request $request, BaseHttpResponse $response)
    {
        return $response->setData(new UserResource($request->user()));
    }

    /**
     * Update Avatar
     *
     * @bodyParam avatar file required Avatar file.
     *
     * @group Profile
     * @authenticated
     */
    public function updateAvatar(Request $request, BaseHttpResponse $response)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => RvMedia::imageValidationRule(),
        ]);

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!') . ' ' . implode(' ', $validator->errors()->all()) . '.');
        }
        try {
            $file = RvMedia::handleUpload($request->file('avatar'), 0, 'uploads/client');
            if (Arr::get($file, 'error') !== true) {
                $user = $request->user();
                $user->update(['avatar' => $file['data']->url]);

                return $response
                    ->setData([
                        'avatar' => RvMedia::url($user->avatar),
                    ])
                    ->setMessage(__('Update avatar successfully!'));
            }

            return $response
                ->setError()
                ->setMessage(__('Update failed!'));
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    /**
     * Update profile
     *
     * @bodyParam name string required First name.
     * @bodyParam display_name string required display name.
     * @bodyParam email string Email.
     * @bodyParam birthday string 
     * @bodyParam summary string 
     * @bodyParam address string 
     * @bodyParam infomation string
     * @bodyParam phone string required Phone.
     *
     * @group Profile
     * @authenticated
     */
    public function updateProfile(Request $request, BaseHttpResponse $response)
    {
        $userId = $request->user()->id;

        $validator = Validator::make($request->input(), [
            'name' => 'required|max:120|min:2',
            'display_name' => 'required|max:120|min:2',
            'phone' => 'required|max:15|min:8',
            'birthday' => 'required|max:15|min:8',
            'summary' => 'nullable',
            'address' => 'nullable',
            'infomation' => 'nullable',
            'email' => 'nullable|max:60|min:6|email|unique:admin_users,email,' . $userId,
        ]);

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!') . ' ' . implode(' ', $validator->errors()->all()) . '.');
        }

        try {
            $request->user()->update($request->input());

            return $response
                ->setData(new UserResource($request->user()))
                ->setMessage(__('Update profile successfully!'));
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    /**
     * Update password
     *
     * @bodyParam password string required The new password of user.
     *
     * @group Profile
     * @authenticated
     */
    public function updatePassword(Request $request, BaseHttpResponse $response)
    {
        $validator = Validator::make($request->input(), [
            'password' => 'required|min:6|max:60',
        ]);

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!') . ' ' . implode(' ', $validator->errors()->all()) . '.');
        }

        $request->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);
        // remove token after change pass
        $request->user()->tokens()->delete();
        return $response->setMessage(trans('Core::admin.update_success'));
    }
}
