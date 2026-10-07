<?php

return [
    'email_templates' => [
        'contact_enquiry' => [
            'subject' => 'We have received your message — KHCWW',
            'body' => "Dear {{member_name}},\n\nThank you for contacting the Kirinyaga Health Care Workers Welfare. We have received your message and a member of our team will respond shortly.\n\nReference: {{registration_reference}}\n\nKind regards,\nKHCWW Team",
        ],
        'registration_confirmation' => [
            'subject' => 'Registration received — KHCWW',
            'body' => "Dear {{member_name}},\n\nYour registration with the Kirinyaga Health Care Workers Welfare has been received.\n\nReference: {{registration_reference}}\nRegistration fee: {{amount}}\n\nKind regards,\nKHCWW Team",
        ],
        'welcome' => [
            'subject' => 'Welcome to KHCWW',
            'body' => "Dear {{member_name}},\n\nWelcome to the Kirinyaga Health Care Workers Welfare. Your membership is now active.\n\nKind regards,\n{{organization_name}}",
        ],
    ],
];
