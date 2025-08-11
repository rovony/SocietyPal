<?php

namespace App\Http\Controllers;


use App\Models\Contact;
use App\Models\Society;
use App\Models\FrontFaq;
use App\Enums\PackageType;
use App\Models\CustomWebPage;
use App\Models\FrontDetail;
use App\Models\FrontFeature;
use App\Models\LanguageSetting;
use App\Models\FrontReviewSetting;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Module;
use App\Models\Package;


class HomeController extends Controller
{

    use AppBoot;

    public function landing()
    {

        $this->showInstall();

        if (global_setting()->landing_site_type == 'custom') {
            return response(file_get_contents(global_setting()->landing_site_url));
        }

        $modules = Module::all();

        $packages = Package::with('modules')
            ->where('package_type', '!=', PackageType::DEFAULT)
            ->where('package_type', '!=', PackageType::TRIAL)
            ->where('is_private', false)
            ->orderBy('sort_order')
            ->get();

        $trialPackage = Package::where('package_type', PackageType::TRIAL)->first();
        $language = request()->get('lang', app()->getLocale());
        $languageSetting = LanguageSetting::where('language_code', $language)->first();
        $languageId = $languageSetting ? $languageSetting->id : null;
        $frontDetails = FrontDetail::where('language_setting_id', $languageId)->first();
        $frontFeatures = FrontFeature::where('language_setting_id', $languageId)->get();
        $frontReviews = FrontReviewSetting::where('language_setting_id', $languageId)->get();
        $frontFaqs = FrontFaq::where('language_setting_id', $languageId)->get();
        $frontContact = Contact::where('language_setting_id', $languageId)->first();
        $customMenu = CustomWebPage::all();

        if(global_setting()->landing_type == 'static'){
            return view('landing.index', compact('packages', 'modules','trialPackage',));

        }else {
            return view('landing.dynamic-index', compact('packages', 'modules', 'trialPackage' , 'frontDetails', 'frontFeatures', 'frontReviews', 'frontFaqs', 'frontContact' , 'customMenu'));

        }


        return view('landing.index', compact('packages', 'modules', 'trialPackage'));
    }

    public function signup()
    {
        if (global_setting()->disable_landing_site) {
            return view('auth.society_register');
        }

        return view('auth.society_signup');
    }

    public function customerLogout()
    {
        session()->flush();
        return redirect('/');
    }

    public function manifest()
    {
        $hash = request()->query('hash', '');

        if ($hash == 'landing') {
            $slug = 'super-admin/';
        } else {
            $slug = 'society/' . $hash . '/';
        }

        $basePath = '';

        if (!empty($hash)) {
            $parsedUrl = parse_url($hash);
            if (isset($parsedUrl['path'])) {
                $basePath = url($parsedUrl['path']);
            }
            if (isset($parsedUrl['query'])) {
                $queryParams = '?' . $parsedUrl['query'];
            }
        } else {
            $basePath = url('/');
        }

        $queryParams = $queryParams ?? '';

        $firstimagePath = public_path('user-uploads/favicons/' . $slug . 'android-chrome-192x192.png');
        $secondimagePath = public_path('user-uploads/favicons/' . $slug . 'android-chrome-512x512.png');
        $firsticonUrl = File::exists($firstimagePath) ? asset('user-uploads/favicons/' . $slug . 'android-chrome-192x192.png') : asset('img/192x192.png');
        $secondiconUrl = File::exists($secondimagePath) ? asset('user-uploads/favicons/' . $slug . 'android-chrome-512x512.png') : asset('img/512x512.png');
        $globalSetting = global_setting();

        $society = Society::where('hash', $hash)->first();
        return response()->json([

            "name" => $society ? $society->name : $globalSetting->name,
            "short_name" => $society ? $society->name : $globalSetting->name,
            "description" =>  $society ? $society->name : $globalSetting->name,
            "start_url_base" => $basePath,
            "query_params" => $queryParams,
            "display" => "standalone",
            "background_color" => "#ffffff",
            "theme_color" => "#000000",
            "icons" => [
                [
                    'src' => $firsticonUrl,
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => $secondiconUrl,
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ]
        ]);
    }
}
