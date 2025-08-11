<?php

namespace App\Observers;

use App\Models\Contact;
use App\Models\FrontDetail;
use App\Models\FrontFaq;
use App\Models\FrontFeature;
use App\Models\FrontReviewSetting;
use App\Models\LanguageSetting;

class LanguageSettingObserver
{

    public function saved(LanguageSetting $languageSetting)
    {
        if ($languageSetting->active == 1) {
            if (FrontDetail::where('language_setting_id',$languageSetting->id)->first()) {
                return true;
            }

            $this->detail($languageSetting->id);
            $this->features($languageSetting->id);
            $this->review($languageSetting->id);
            $this->frontFaq($languageSetting->id);
        }
    }

    public function detail($languageId)
    {
        $trFrontDetail = new FrontDetail();
        $trFrontDetail->language_setting_id = $languageId;

        $trFrontDetail->header_title = 'Simplify Society Management with Our SaaS Solution';
        $trFrontDetail->header_description = 'An all-in-one platform to efficiently manage apartments, residents and maintenance. Reduce administrative burden while improving operational efficiency.';
        $trFrontDetail->image = 'dashboard.png';

        $trFrontDetail->feature_with_image_heading = 'Streamline Your Society Operations';
        $trFrontDetail->feature_with_icon_heading = 'Advanced Features to Transform Your Society Management';

        $trFrontDetail->review_heading = 'Success Stories from Society Administrators';

        $trFrontDetail->price_heading = 'Straightforward Pricing Plans';
        $trFrontDetail->price_description = 'One comprehensive plan with all essential features at an affordable price.';

        $trFrontDetail->faq_heading = 'Common Questions Answered';
        $trFrontDetail->faq_description = 'Answers to the most frequently asked questions.';

        $trFrontDetail->contact_heading = 'Contact';
        $trFrontDetail->footer_copyright_text = '© 2025 SocietyPro. All Rights Reserved.';
        $trFrontDetail->created_at = now();
        $trFrontDetail->updated_at = now();
        $trFrontDetail->save();

    }

    public function features($languageId)
    {
        $features = [
            [
                'description' => 'Centralize apartment records, ownership details and resident information. Automate rent collection, lease management and resident communications for a seamless experience.',
                'icon' => '',
                'image' => 'null',
                'language_setting_id' =>$languageId,
                'title' => 'Simplified Resident Management',
                'type' => 'image',


            ],
            [
                'language_setting_id' =>$languageId,
                'title' => 'Optimized Service Provider Coordination',
                'description' => 'Streamline management of housekeeping, security and maintenance staff. Monitor schedules, track performance and ensure timely service delivery across all areas.',
                'type' => 'image',
                'icon' => '',
                'image'=> 'null'

            ],
            [
                'language_setting_id' =>$languageId,
                'title' => 'Smart Amenity Management',
                'description' => 'Enable residents to conveniently book common facilities like clubhouse, gym and pool. Prevent double bookings with an automated scheduling system that ensures fair access.',
                'type' => 'image',
                'icon' => '',
                'image'=> 'null'

            ],

        ];


        FrontFeature::insert($features);

        $features = [
            [
                'description' => 'Automated Billing for Rent, Utilities and Maintenance',
                'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5M11.5 4a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"></path>
                    <path d="M2.354.646a.5.5 0 0 0-.801.13l-.5 1A.5.5 0 0 0 1 2v13H.5a.5.5 0 0 0 0 1h15a.5.5 0 0 0 0-1H15V2a.5.5 0 0 0-.053-.224l-.5-1a.5.5 0 0 0-.8-.13L13 1.293l-.646-.647a.5.5 0 0 0-.708 0L11 1.293l-.646-.647a.5.5 0 0 0-.708 0L9 1.293 8.354.646a.5.5 0 0 0-.708 0L7 1.293 6.354.646a.5.5 0 0 0-.708 0L5 1.293 4.354.646a.5.5 0 0 0-.708 0L3 1.293zm-.217 1.198.51.51a.5.5 0 0 0 .707 0L4 1.707l.646.647a.5.5 0 0 0 .708 0L6 1.707l.646.647a.5.5 0 0 0 .708 0L8 1.707l.646.647a.5.5 0 0 0 .708 0L10 1.707l.646.647a.5.5 0 0 0 .708 0L12 1.707l.646.647a.5.5 0 0 0 .708 0l.509-.51.137.274V15H2V2.118z"></path>
                </svg>',
                'icon' => 'bi-newspaper',
                'language_setting_id' =>$languageId,
                'type' => 'icon',
                'title' => 'Smart Billing System',
            ],
            [
                'description' => 'Integrated Payments via Stripe and Razorpay',
                'language_setting_id' =>$languageId,
                'title' => 'Secure Payment Processing',
                'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                <path
                    d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.226 5.385c-.584 0-.937.164-.937.593 0 .468.607.674 1.36.93 1.228.415 2.844.963 2.851 2.993C11.5 11.868 9.924 13 7.63 13a7.7 7.7 0 0 1-3.009-.626V9.758c.926.506 2.095.88 3.01.88.617 0 1.058-.165 1.058-.671 0-.518-.658-.755-1.453-1.041C6.026 8.49 4.5 7.94 4.5 6.11 4.5 4.165 5.988 3 8.226 3a7.3 7.3 0 0 1 2.734.505v2.583c-.838-.45-1.896-.703-2.734-.703" />
                </svg>',
                'icon' => 'bi-bootstrap-fill',
                'type' => 'icon',
            ],

            [
                'description' => 'Individual Attendance Tracking for Service Providers',
                'language_setting_id' =>$languageId,
                'title' => 'Staff Management',
                'type' => 'icon',
                'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"></path>
                    </svg>',
                'icon' => 'bi-people',
            ],
            [
                'description' => 'Digital Visitor Registration and Tracking',
                'image' => '<svg class="transition duration-75 size-6 text-skin-base dark:text-skin-base" fill="currentColor" viewBox="0 -0.5 25 25" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" d="M16,6 L20,6 C21.1045695,6 22,6.8954305 22,8 L22,16 C22,17.1045695 21.1045695,18 20,18 L16,18 L16,19.9411765 C16,21.0658573 15.1177541,22 14,22 L4,22 C2.88224586,22 2,21.0658573 2,19.9411765 L2,4.05882353 C2,2.93414267 2.88224586,2 4,2 L14,2 C15.1177541,2 16,2.93414267 16,4.05882353 L16,6 Z M20,11 L16,11 L16,16 L20,16 L20,11 Z M14,19.9411765 L14,4.05882353 C14,4.01396021 13.9868154,4 14,4 L4,4 C4.01318464,4 4,4.01396021 4,4.05882353 L4,19.9411765 C4,19.9860398 4.01318464,20 4,20 L14,20 C13.9868154,20 14,19.9860398 14,19.9411765 Z M5,19 L5,17 L7,17 L7,19 L5,19 Z M8,19 L8,17 L10,17 L10,19 L8,19 Z M11,19 L11,17 L13,17 L13,19 L11,19 Z M5,16 L5,14 L7,14 L7,16 L5,16 Z M8,16 L8,14 L10,14 L10,16 L8,16 Z M11,16 L11,14 L13,14 L13,16 L11,16 Z M13,5 L13,13 L5,13 L5,5 L13,5 Z M7,7 L7,11 L11,11 L11,7 L7,7 Z M20,9 L20,8 L16,8 L16,9 L20,9 Z">
                            </path>
                        </g>
                    </svg>',
                'icon' => 'print-icon',
                'language_setting_id' =>$languageId,
                'title' => 'Visitor Access Control',
                'type' => 'icon',
            ],
            [
                'description' => 'Centralized Notice Board for Communications',
                'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                <path d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882zm3.515 7.008L14.438 10 8 13.433 1.562 10 4.25 8.567l3.515 1.874a.5.5 0 0 0 .47 0zM8 9.433 1.562 6 8 2.567 14.438 6z"></path>
                </svg>',
                'icon' => 'bi-box',
                'language_setting_id' =>$languageId,
                'title' => 'Community Updates',
                'type' => 'icon',
            ],
            [
                'description' => 'Systematic Handling of Resident Issues',
                'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-ticket-perforated text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                <path
                    d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5v2a.5.5 0 0 1-.5.5.992.992 0 0 0 0 2 .5.5 0 0 1 .5.5v2A1.5 1.5 0 0 1 14.5 14h-13A1.5 1.5 0 0 1 0 12.5v-2a.5.5 0 0 1 .5-.5.992.992 0 0 0 0-2A.5.5 0 0 1 0 6.5v-2ZM1.5 4a.5.5 0 0 0-.5.5V6a2 2 0 0 1 0 4v1.5a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10a2 2 0 0 1 0-4V4.5a.5.5 0 0 0-.5-.5h-13ZM4 7.5v1a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-7a.5.5 0 0 0-.5.5Z" />
            </svg>',
                'icon' => 'bi bi-ticket-perforated',
                'language_setting_id' =>$languageId,
                'title' => 'Complaint Resolution',
                'type' => 'icon',
            ],
            [
                'description' => 'Digital Parking Space Management System',
                'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                class="text-skin-base dark:text-skin-base size-6" viewBox="0 0 24 24">
                <path d="M5 11V7.5C5 4.42 7.42 2 10.5 2h3c3.08 0 5.5 2.42 5.5 5.5V11h1c1.1 0 2 .9 2 2v6h-2v2h-2v-2H6v2H4v-2H2v-6c0-1.1.9-2 2-2zm2 0h10V7.5C17 5.57 15.43 4 13.5 4h-3C8.57 4 7 5.57 7 7.5zm-3 2v4h2v-4zm14 0v4h2v-4z"/>
            </svg>',
                'language_setting_id' =>$languageId,
                'title' => 'Parking Solutions',
                'icon' => null,
                'type' => 'icon',
            ],
            [
                'description' => 'Insightful Reports for Better Decisions',
                'language_setting_id' =>$languageId,
                'title' => 'Analytics Dashboard',
                'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"></path>
                </svg>',
                'icon' => 'bi-arrow-right-circle-fill',
                'type' => 'icon',
            ],


        ];

        FrontFeature::insert($features);

    }

    public function review($languageId)
    {
        $reviews = [
            [
                'language_setting_id' =>$languageId,
                'reviewer_name' => 'Michael Davis',
                'reviewer_designation' => 'Owner of Greenview Residency',
                'reviews' => 'This platform has revolutionized our society management process. Having everything from maintenance to resident services on a single dashboard has significantly improved our operational efficiency.',
            ],
            [
                'language_setting_id' =>$languageId,
                'reviewer_name' => 'Emily Thompson',
                'reviewer_designation' => 'Manager at Lakeside Grill',
                'reviews' => "The integrated payment system has been a game-changer. Our residents appreciate the convenience, and we've noticed faster collection of dues and maintenance fees. ",
            ],

        ];

        FrontReviewSetting::insert($reviews);

    }
    public function frontFaq($languageId)
    {

        $client = [
            [
                'language_setting_id' =>$languageId,
                'question' => 'How can I contact customer support?',
                'answer' => 'Our dedicated support team is available via email to assist you with any questions or technical issues.',
            ],
            [
                'language_setting_id' =>$languageId,
                'question' => 'Is the software easy to use?',
                'answer' => 'Yes, our software is designed to be user-friendly and intuitive.',
            ],
        ];

        FrontFaq::insert($client);
    }

    public function contact($languageId)
    {
        Contact::insert([
            'language_setting_id' => $languageId,
            'contact_company' => 'Bond Hobbs Inc',
            'address' => '957 Jamie Station, Lamontborough, SD 27319-9459',
            'email' => 'support@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
