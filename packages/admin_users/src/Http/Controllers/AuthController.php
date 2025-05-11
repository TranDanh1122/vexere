<?php

namespace DreamTeam\AdminUser\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use DreamTeam\AdminUser\Mail\ForgotPassword;
use DreamTeam\Base\Events\ClearCacheEvent;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'admin';
    protected int|bool|string $gg2fa;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    //hàm khởi tạo xác thực là guest của admin khi truy cập
    public function __construct()
    {
        $option = getOption('googleAuthenticate', '', false);
        $isEnabled = $option['enabled'] ?? 0;
        $this->gg2fa = $isEnabled;
    }

    public function login()
    {
        if (Auth::guard('admin')->check()) {
            return redirect(route('admin.home'));
        } else {
            return view('AdminUser::auth.login');
        }
    }

    public function setLogin(Request $requests)
    {
        $name = $requests->name;
        $remember = $requests->remember == 'true';

        // kiểm tra có phải Email không, nếu là email thì sẽ login theo Email
        $field = filter_var($name, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $requests->merge([$field => $name]);
        // Thực thi đăng nhập
        $credentials = $requests->only($field, 'password');
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            if (Auth::guard('admin')->user()->status == 1 || Auth::guard('admin')->user()->id == 1) {
                systemLogs('login');
                return [
                    'status' => 1,
                    'message' => __('AdminUser::admin.login.login_success'),
                    'url' => $url ?? route('admin.home')
                ];
            } else {
                Auth::guard('admin')->logout();
                return [
                    'status' => 2,
                    'message' => __('AdminUser::admin.login.login_status_permision'),
                ];
            }
        } else {
            return [
                'status' => 2,
                'message' => __('AdminUser::admin.login.login_error'),
            ];
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        if (!Auth::check() && !Auth::guard('admin')->check()) {
            $request->session()->flush();
            $request->session()->regenerate();
        }
        return redirect(route('admin.login'));
    }

    // View đổi mật khẩu
    public function forgotPassword(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect(route('admin.home'));
        } else {
            return view('AdminUser::auth.forgot_password');
        }
    }
    public function changePassword(Request $request)
    {
        $token = $request->token;
        // Kiểm tra tồn tại token
        if (isset($token) && !empty($token)) {
            $admin_password_reset = DB::table('admin_password_resets')->where('token', $token)->first();
            // Kiểm tra tồn tại admin_password_resets
            if (!empty($admin_password_reset)) {
                $admin_users = DB::table('admin_users')->where('email', $admin_password_reset->email)->first();
                return view('AdminUser::auth.change_password', compact('admin_users'));
            } else {
                return redirect(route('admin.login'));
            }
        } else {
            return redirect(route('admin.login'));
        }
    }
    // Ajax quên mật khẩu
    public function setForgotPassword(Request $requests)
    {
        $email = $requests->email;
        $check_admin_users = \DreamTeam\AdminUser\Models\AdminUser::where('email', $email)->first();
        if ($check_admin_users == null) {
            return [
                'status' => 2,
                'message' => __('AdminUser::admin.login.email_not_exist'),
            ];
        } else {
            $check_reset_pass = DB::table('admin_password_resets')->where('email', $email)->first();
            // nếu chưa có record trong DB thì sinh chuỗi ngẫu nhiên
            if ($check_reset_pass == null) {
                $token = randString(60);
            } else {
                $token = $check_reset_pass->token;
            }
            // Link đổi mật khẩu
            $links = route('admin.change_password', ['token' => $token]);
            // check email config
            event(new ClearCacheEvent());
            try {
                config([
                    'mail.default'                        => 'custom',
                    'mail.mailers.custom.transport'       => config('mail.mailers.custom.transport') ?? '',
                    'mail.mailers.custom.host'            => config('mail.mailers.custom.host') ?? '',
                    'mail.mailers.custom.port'            => config('mail.mailers.custom.port') ?? '',
                    'mail.mailers.custom.encryption'      => config('mail.mailers.custom.encryption') ?? '',
                    'mail.mailers.custom.username'        => config('mail.mailers.custom.username') ?? '',
                    'mail.mailers.custom.password'        => config('mail.mailers.custom.password') ?? '',
                    'mail.from.address'                   => config('mail.mailers.custom.username') ?? '',
                    'mail.from.name'                      => getSiteName(),
                ]);
                Mail::to($email)->send(new ForgotPassword([
                    'emails' => $email,
                    'links' => $links
                ]));
            } catch (Exception $e) {
                Log::error($e);
                return [
                    'status' => 2,
                    'message' => __('Translate::admin.error_message_catch'),
                ];
            }

            // Thêm bản ghi vào DB
            if ($check_reset_pass == null) {
                DB::table('admin_password_resets')->insert([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
            // Trả kết quả
            return [
                'status' => 1,
                'message' => __('AdminUser::admin.login.forgot_success'),
            ];
        }
    }

    public function setChangePassword(Request $requests, $id)
    {
        $password = $requests->password;
        $password_comfirm = $requests->password_comfirm;
        if (!isset($password) && isset($password_comfirm)) {
            return [
                'status' => 2,
                'message' => __('AdminUser::admin.forgot_password.required_info'),
            ];
        } else if (isset($password) && empty($password)) {
            return [
                'status' => 2,
                'message' => __('AdminUser::admin.forgot_password.required_password'),
            ];
        } else if (isset($password_comfirm) && empty($password_comfirm)) {
            return [
                'status' => 2,
                'message' => __('AdminUser::admin.forgot_password.required_password_comfirm'),
            ];
        } else if ($password_comfirm != $password) {
            return [
                'status' => 2,
                'message' => __('AdminUser::admin.forgot_password.required_equal'),
            ];
        } else if (passwordStrength($password) < 3) {
            return [
                'status' => 2,
                'message' => __('AdminUser::admin.forgot_password.required_strong'),
            ];
        } else {
            $admin_users = \DreamTeam\AdminUser\Models\AdminUser::where('id', $id)->first();
            \DreamTeam\AdminUser\Models\AdminUser::where('id', $id)->update([
                'password' => bcrypt($password)
            ]);
            DB::table('admin_password_resets')->where('email', $admin_users->email)->delete();
            if (Auth::guard('admin')->attempt(['email' => $admin_users->email, 'password' => $password])) {
                return [
                    'status' => 1,
                    'message' => __('AdminUser::admin.login.login_success'),
                    'url' => route('admin.home')
                ];
            } else {
                return [
                    'status' => 1,
                    'message' => __('AdminUser::admin.update_success'),
                    'url' => route('admin.login')
                ];
            }
        }
    }



    // đăng ký QRCODE đối với lần đầu login
    public function registerQr(Request $request)
    {
        $user = Auth::guard('admin')->user();
        // Initialise the 2FA class
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        // Add the secret key to the registration data
        $user->google2fa_secret = $google2fa->generateSecretKey();
        $user->save();

        $google2fa_url = $google2fa->getQRCodeInline(
            getSiteName(),
            $user->email,
            $user->google2fa_secret
        );
        $secret_key = $user->google2fa_secret;
        $data = array(
            'user' => $user,
            'secret' => $secret_key,
            'google2fa_url' => $google2fa_url
        );
        // Pass the QR barcode image to our view
        return view('AdminUser::auth.google.register', compact('data'));
    }

    public function faVerify(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        $secret = $request->input('one_time_password');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);
        if ($valid) {
            return redirect(route('admin.home'));
        }
        return redirect()->back()->withErrors([__('AdminUser::admin.validate.invalid_otp')]);
    }

    public function checkAuth()
    {
        if (!Auth::guard('admin')->check()) {
            return [
                'status' => 0,
                'link' => route('admin.login')
            ];
        }
        return ['status' => 1];
    }
}
