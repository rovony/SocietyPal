<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Models\OnboardingStep;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{

    use AppBoot;
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {

            public function toResponse($request)
            {
                session(['user' => User::find(user()->id)]);

                if (user()->hasRole('Admin_' . user()->society_id)) {
                    $onboardingSteps = OnboardingStep::where('society_id', user()->society->id)->first();

                    if (
                        $onboardingSteps
                        && (
                            !$onboardingSteps->add_tower_completed
                            || !$onboardingSteps->add_floor_completed
                            || !$onboardingSteps->add_apartment_completed
                            || !$onboardingSteps->add_parking_completed
                        )
                    ) {
                        return redirect(RouteServiceProvider::ONBOARDING_STEPS);
                    }
                }

                if (user()->hasRole('Super Admin')) {
                    return redirect(RouteServiceProvider::SUPERADMIN_HOME);
                }

                return redirect(session()->has('url.intended') ? session()->get('url.intended') : RouteServiceProvider::HOME);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::authenticateUsing(function (Request $request) {

            \Illuminate\Support\Facades\App::setLocale(session('locale') ?? global_setting()->locale ?? 'en');

            $rules = [
                'email' => 'required|email:rfc|regex:/(.+)@(.+)\.(.+)/i'
            ];

            $request->validate($rules);

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {

                User::validateLoginActiveDisabled($user);

                return $user;
            }
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            $this->showInstall();

            $this->checkMigrateStatus();

            if (!$this->isLegal()) {
                if (!module_enabled('Subdomain')) {
                    return redirect('verify-purchase');
                }

                // We will only show verify page for super-admin-login
                // We will check it's opened on main or not
                if (Str::contains(request()->url(), 'super-admin-login')) {
                    return redirect('verify-purchase');
                }
            }

            return view('auth.login');
        });

        Fortify::authenticateUsing(function ($request) {

            if (global_setting()->google_recaptcha_status === 'active' && global_setting()->google_recaptcha_v3_status === 'active') {
                $recaptchaToken = $request->input('g_recaptcha');
                if (!$recaptchaToken) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'g_recaptcha' => ['Captcha verification is required.'],
                    ]);
                }

                $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => global_setting()->google_recaptcha_v3_secret_key,
                    'response' => $recaptchaToken,
                    'remoteip' => $request->ip(),
                ]);

                $responseBody = $response->json();


                if (!isset($responseBody['success'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'g_recaptcha' => ['Captcha verification failed. Please try again.'],
                    ]);
                }
            }

            $user = \App\Models\User::where('email', $request->email)->first();

            if (
                $user &&
                \Illuminate\Support\Facades\Hash::check($request->password, $user->password) &&
                $user->status === 'active'
            ) {
                return $user;
            }

            return null;
        });
    }
    public function checkMigrateStatus()
    {
        return check_migrate_status();
    }
}
