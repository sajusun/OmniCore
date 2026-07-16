<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Helpers\Helper;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('backend.settings.general_settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'copyright' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'favicon' => 'nullable|image|mimes:png,ico|max:2048',
            'map_embed_code' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'business_time' => 'nullable|string|max:255',
        ]);

        $setting = Setting::first() ?? new Setting();

        $data = $request->except(['_token', '_method', 'logo', 'favicon']);

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Helper::fileDelete(public_path($setting->logo));
            }
            $data['logo'] = Helper::fileUpload($request->file('logo'), 'settings', 'logo');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Helper::fileDelete(public_path($setting->favicon));
            }
            $data['favicon'] = Helper::fileUpload($request->file('favicon'), 'settings', 'favicon');
        }

        $setting->fill($data)->save();

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'logo_height'   => 'nullable|string',
            'logo_width'    => 'nullable|string',
        ]);

        $setting = Setting::first() ?? new Setting();

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Helper::fileDelete(public_path($setting->logo));
            }
            $setting->logo = Helper::fileUpload($request->file('logo'), '/settings', 'logo');
        }
        
        $setting->logo_width = $request->logo_width ?? $setting->logo_width;
        $setting->logo_height = $request->logo_height ?? $setting->logo_height;
        $setting->save();


        return redirect()->back()->with('success', 'Logo updated successfully');
    }

    public function viewLogo()
    {
        $setting = Setting::first();
        return view('backend.settings.logo_settings', compact('setting'));
    }

    // ─── ENV Settings ────────────────────────────────────────────────────────

    /**
     * Show the env settings page with parsed key-value data.
     */
    public function env()
    {
        $env = $this->parseEnv();
        return view('backend.settings.env_settings', compact('env'));
    }

    /**
     * Tab: Application — APP_* and core URLs.
     */
    public function updateEnvApp(Request $request)
    {
        $request->validate([
            'app_name'     => 'nullable|string|max:255',
            'app_env'      => 'nullable|in:local,staging,production',
            'app_url'      => 'nullable|string|max:255',
            'app_timezone' => 'nullable|string|max:100',
            'app_debug'    => 'nullable|in:true,false',
            'support_mail' => 'nullable|email|max:255',
            'frontend_url' => 'nullable|string|max:255',
            'backend_url'  => 'nullable|string|max:255',
        ]);

        foreach ([
            'APP_NAME'     => $request->input('app_name'),
            'APP_ENV'      => $request->input('app_env'),
            'APP_URL'      => $request->input('app_url'),
            'APP_TIMEZONE' => $request->input('app_timezone'),
            'APP_DEBUG'    => $request->input('app_debug'),
            'SUPPORT_MAIL' => $request->input('support_mail'),
            'FRONTEND_URL' => $request->input('frontend_url'),
            'BACKEND_URL'  => $request->input('backend_url'),
        ] as $key => $value) {
            $this->writeEnvKey($key, $value);
        }

        if ($request->has('_env_tab')) {
            session()->flash('_env_tab', $request->input('_env_tab'));
        }

        return redirect()->back()->with('success', 'Application settings updated successfully.');
    }

    /**
     * Tab: JWT — token auth configuration.
     */
    public function updateEnvJwt(Request $request)
    {
        $request->validate([
            'jwt_secret'                => 'nullable|string|max:255',
            'jwt_ttl'                   => 'nullable|integer|min:1',
            'jwt_refresh_ttl'           => 'nullable|integer|min:1',
            'jwt_algo'                  => 'nullable|string|max:20',
            'jwt_blacklist_enabled'     => 'nullable|in:true,false',
            'jwt_blacklist_grace_period'=> 'nullable|integer|min:0',
            'jwt_leeway'                => 'nullable|integer|min:0',
        ]);

        foreach ([
            'JWT_SECRET'                 => $request->input('jwt_secret'),
            'JWT_TTL'                    => $request->input('jwt_ttl'),
            'JWT_REFRESH_TTL'            => $request->input('jwt_refresh_ttl'),
            'JWT_ALGO'                   => $request->input('jwt_algo'),
            'JWT_BLACKLIST_ENABLED'      => $request->input('jwt_blacklist_enabled'),
            'JWT_BLACKLIST_GRACE_PERIOD' => $request->input('jwt_blacklist_grace_period'),
            'JWT_LEEWAY'                 => $request->input('jwt_leeway'),
        ] as $key => $value) {
            $this->writeEnvKey($key, $value);
        }

        if ($request->has('_env_tab')) {
            session()->flash('_env_tab', $request->input('_env_tab'));
        }

        return redirect()->back()->with('success', 'JWT settings updated successfully.');
    }

    /**
     * Tab: Firebase — push notification configuration.
     */
    public function updateEnvFirebase(Request $request)
    {
        $request->validate([
            'firebase_project'                => 'nullable|string|max:100',
            'firebase_credentials'            => 'nullable|string|max:500',
            'firebase_database_url'           => 'nullable|string|max:255',
            'firebase_storage_default_bucket' => 'nullable|string|max:255',
        ]);

        foreach ([
            'FIREBASE_PROJECT'                => $request->input('firebase_project'),
            'FIREBASE_CREDENTIALS'            => $request->input('firebase_credentials'),
            'FIREBASE_DATABASE_URL'           => $request->input('firebase_database_url'),
            'FIREBASE_STORAGE_DEFAULT_BUCKET' => $request->input('firebase_storage_default_bucket'),
        ] as $key => $value) {
            $this->writeEnvKey($key, $value);
        }

        if ($request->has('_env_tab')) {
            session()->flash('_env_tab', $request->input('_env_tab'));
        }

        return redirect()->back()->with('success', 'Firebase settings updated successfully.');
    }

    /**
     * Tab: Verification — OTP / token auth config.
     * Key names match config/verification.php exactly.
     */
    public function updateEnvVerification(Request $request)
    {
        $request->validate([
            'verification_default_type'          => 'nullable|in:otp,token',
            'verification_otp_digits'            => 'nullable|integer|between:4,8',
            'verification_otp_expiry_minutes'    => 'nullable|integer|min:1',
            'verification_max_attempts'          => 'nullable|integer|min:1',
            'verification_token_length'          => 'nullable|integer|between:16,128',
            'verification_token_expiry_minutes'  => 'nullable|integer|min:1',
            'verification_resend_cooldown_seconds'=> 'nullable|integer|min:0',
            'verification_max_resend_requests'   => 'nullable|integer|min:1',
            'verification_block_hours'           => 'nullable|integer|min:1',
            'verification_success_redirect_url'  => 'nullable|string|max:255',
            'verification_failed_redirect_url'   => 'nullable|string|max:255',
        ]);

        foreach ([
            'VERIFICATION_DEFAULT_TYPE'           => $request->input('verification_default_type'),
            'VERIFICATION_OTP_DIGITS'             => $request->input('verification_otp_digits'),
            'VERIFICATION_OTP_EXPIRY_MINUTES'     => $request->input('verification_otp_expiry_minutes'),
            'VERIFICATION_MAX_ATTEMPTS'           => $request->input('verification_max_attempts'),
            'VERIFICATION_TOKEN_LENGTH'           => $request->input('verification_token_length'),
            'VERIFICATION_TOKEN_EXPIRY_MINUTES'   => $request->input('verification_token_expiry_minutes'),
            'VERIFICATION_RESEND_COOLDOWN_SECONDS'=> $request->input('verification_resend_cooldown_seconds'),
            'VERIFICATION_MAX_RESEND_REQUESTS'    => $request->input('verification_max_resend_requests'),
            'VERIFICATION_BLOCK_HOURS'            => $request->input('verification_block_hours'),
            'VERIFICATION_SUCCESS_REDIRECT_URL'   => $request->input('verification_success_redirect_url'),
            'VERIFICATION_FAILED_REDIRECT_URL'    => $request->input('verification_failed_redirect_url'),
        ] as $key => $value) {
            $this->writeEnvKey($key, $value);
        }

        if ($request->has('_env_tab')) {
            session()->flash('_env_tab', $request->input('_env_tab'));
        }

        return redirect()->back()->with('success', 'Verification settings updated successfully.');
    }

    /**
     * Tab: System — feature toggles and notification channels.
     */
    public function updateEnvSystem(Request $request)
    {
        $request->validate([
            'enable_role_management'  => 'nullable|in:true,false',
            'in_app_notifications'    => 'nullable|in:true,false',
            'notification_database'   => 'nullable|in:true,false',
            'notification_firebase'   => 'nullable|in:true,false',
            'notification_broadcast'  => 'nullable|in:true,false',
            'notification_mail'       => 'nullable|in:true,false',
            'sms'                     => 'nullable|in:on,off',
            'mail'                    => 'nullable|in:on,off',
            'reverb'                  => 'nullable|in:on,off',
            'recaptcha_enable'        => 'nullable|in:yes,no',
            'pagination'              => 'nullable|integer|min:1|max:200',
            'google_maps_api_key'     => 'nullable|string|max:255',
        ]);

        foreach ([
            'ENABLE_ROLE_MANAGEMENT'  => $request->input('enable_role_management'),
            'IN_APP_NOTIFICATIONS'    => $request->input('in_app_notifications'),
            'NOTIFICATION_DATABASE'   => $request->input('notification_database'),
            'NOTIFICATION_FIREBASE'   => $request->input('notification_firebase'),
            'NOTIFICATION_BROADCAST'  => $request->input('notification_broadcast'),
            'NOTIFICATION_MAIL'       => $request->input('notification_mail'),
            'SMS'                     => $request->input('sms'),
            'MAIL'                    => $request->input('mail'),
            'REVERB'                  => $request->input('reverb'),
            'RECAPTCHA_ENABLE'        => $request->input('recaptcha_enable'),
            'PAGINATION'              => $request->input('pagination'),
            'GOOGLE_MAPS_API_KEY'     => $request->input('google_maps_api_key'),
        ] as $key => $value) {
            $this->writeEnvKey($key, $value);
        }

        if ($request->has('_env_tab')) {
            session()->flash('_env_tab', $request->input('_env_tab'));
        }

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }

    // ─── Mail Settings ────────────────────────────────────────────────────────

    /**
     * Show the mail settings page.
     */
    public function mail()
    {
        $env = $this->parseEnv();
        return view('backend.settings.mail_settings', compact('env'));
    }

    /**
     * Tab: Mail — SMTP configuration.
     */
    public function updateMail(Request $request)
    {
        $request->validate([
            'mail_mailer'       => 'nullable|string|max:50',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_scheme'       => 'nullable|string|max:10',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);

        foreach ([
            'MAIL_MAILER'       => $request->input('mail_mailer'),
            'MAIL_HOST'         => $request->input('mail_host'),
            'MAIL_PORT'         => $request->input('mail_port'),
            'MAIL_USERNAME'     => $request->input('mail_username'),
            'MAIL_PASSWORD'     => $request->input('mail_password'),
            'MAIL_SCHEME'       => $request->input('mail_scheme'),
            'MAIL_FROM_ADDRESS' => $request->input('mail_from_address'),
            'MAIL_FROM_NAME'    => $request->input('mail_from_name'),
        ] as $key => $value) {
            $this->writeEnvKey($key, $value);
        }

        return redirect()->back()->with('success', 'Mail settings updated successfully.');
    }

    /**
     * Send a test email.
     */
    public function sendMail(Request $request)
    {
        $request->validate([
            'receiver' => 'required|email',
            'subject'  => 'required|string|max:255',
            'content'  => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw($request->content, function ($message) use ($request) {
                $message->to($request->receiver)
                    ->subject($request->subject);
            });

            return redirect()->back()->with('success', 'Test email sent successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Parse the .env file into an associative array of key => value pairs.
     */
    private function parseEnv(): array
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return [];
        }

        $lines  = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $result = [];

        foreach ($lines as $line) {
            // Skip comment lines
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Write (or update) a single key=value pair in the .env file.
     */
    private function writeEnvKey(string $key, ?string $value): void
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return;
        }

        // Wrap value in quotes if it contains spaces
        $escaped = (str_contains((string) $value, ' '))
            ? '"' . $value . '"'
            : (string) $value;

        $content = file_get_contents($path);

        if (preg_match("/^{$key}=.*/m", $content)) {
            // Key exists — replace it
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$escaped}", $content);
        } else {
            // Key does not exist — append it
            $content .= PHP_EOL . "{$key}={$escaped}";
        }

        file_put_contents($path, $content);
    }
}
