<?php

return array(
    'newUser' => array(
        'subject' => 'Welcome to our application',
        'greeting' => 'Hello ',
        'email' => 'Email: ',
        'password' => 'Password: ',
        'message' => 'Congratulations! Your account has been created successfully.',
        'action' => 'Login to Dashboard',
    ),

    'newMaintenance' => array(
        'subject' => 'New Maintenance Published',
        'greeting' => 'Hello ',
        'message' => 'A new maintenance record has been published.',
        'action' => 'View Maintenance',
    ),

    'societyUpdatedPlan' => array (
        'subject' => 'Society Plan Updated Successfully',
        'greeting' => 'Hello :name!',
        'line1' => 'A society plan has been successfully updated.',
        'line2' => 'Here are the updated plan details:',
        'action' => 'View Dashboard',
        'line4' => 'Thank you for using our service!',
    ),

    'offlineRequestReview' => array(
        'subject' => 'Request for Modification of Package - :site_name',
        'greeting' => 'Hello, :name!',
        'line1' => 'A request for an offline payment to change the package has been made.',
        'line2' => 'Society name: **:society_name**',
        'line3' => 'Package name: **:package_name**',
        'line4' => 'Package type: **:package_type**',
        'line5' => 'Please review the request and take the necessary action.',
        'line6' => 'Thank you for using our application!',
    ),

    'serviceProviderClockedIn' => array(
        'subject' => 'just clocked-in at',
        'greeting' => 'Hello :name,',
        'message' => 'A service provider has just clocked in for your apartment.',
        'service_type' => '**Service Type:** :type',
        'contact_person' => '**Contact Person:** :name',
        'clock_in_date' => '**Clock-In Date:** :date',
        'clock_in_time' => '**Clock-In Time:** :time',
        'added_by' => '**Added By:** :name',
        'thank_you' => 'Thank you for using our service management system!',
        'view_details' => 'View Details',
    ),

    'serviceProviderClockedOut' => array(
        'subject' => 'just clocked-out at',
        'greeting' => 'Hello :name,',
        'message' => 'A service provider has just clocked out for your apartment.',
        'service_type' => '**Service Type:** :type',
        'contact_person' => '**Contact Person:** :name',
        'clock_out_date' => '**Clock-Out Date:** :date',
        'clock_out_time' => '**Clock-Out Time:** :time',
        'added_by' => '**Added By:** :name',
        'thank_you' => 'Thank you for using our service management system!',
        'view_details' => 'View Details',
    ),

    'visitor' => array(
        'newVisitorApprovalNeeded' => 'New Visitor Approval Needed',
        'hello' => 'Hello :name!',
        'visitorApprovalMessage' => 'A new visitor is waiting for approval to enter your apartment.',
        'visitorName' => 'Visitor Name: :name',
        'visitorMobile' => 'Visitor Mobile: :mobile',
        'visitDate' => 'Visit Date: :date',
        'reviewVisitor' => 'Please review and allow or deny access to the visitor using the link below:',
        'allowDenyVisitor' => 'Allow/Deny Visitor',
    ),

    'trialLicenseExpPre' => array (
        'subject' => 'License Expiration Notice',
        'greeting' => 'Hello :name!',
        'line1' => 'Your trial license is about to expire on **:date**.',
        'line2' => 'Please take the necessary actions to renew it.',
        'action' => 'Go to Dashboard',
        'line3' => 'Thank you for using our application!',
    ),

    'trialLicenseExp' => array (
        'subject' => 'Your Trial License Has Expired',
        'greeting' => 'Hello :name!',
        'line1' => 'We wanted to let you know that your trial license has expired.',
        'line2' => 'Society Name: :society_name',
        'action' => 'Go to Dashboard',
    ),

    'subscriptionExpire' => array (
        'subject' => 'Subscription Expiration Notice',
        'greeting' => 'Hello :name!',
        'line1' => 'We are writing to inform you that your subscription for :society_name has expired.',
        'line2' => 'The subscription expired on :date.',
        'action' => 'Renew Subscription',
        'line3' => 'Thank you for using our application!',
    ),

    'event' => array(
        'text' => 'We are excited to announce a new event that has been created and we would like to invite you to join us.',
        'subject' => 'New Event Created: :event_name',
        'greeting' => 'Hello!',
        'where' => 'Location: :where',
        'start_date_time' => 'Start Date & Time: :start',
        'end_date_time' => 'End Date & Time: :end',
        'action' => 'View Event Details',
        'thank_you' => 'Thank you for staying connected!',
    ),

    'forum' => array(
       'forum_subject' => 'New Forum Discussion: :title',
        'greeting' => 'Hello :name,',
        'new_discussion_intro' => 'A new discussion titled ":title" has been started by :creator.',
        'category' => 'Category: :category',
        'view_discussion' => 'View Discussion',
        'thank_you' => 'Thank you for staying engaged with the community!',
    ),
);
