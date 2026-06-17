<?php

return [
    'token' => env('BALE_BOT_TOKEN'),
    'api_base_url' => env('BALE_API_BASE_URL', 'https://tapi.bale.ai'),
    'webhook_secret' => env('BALE_WEBHOOK_SECRET'),
    'support_technical_url' => env('SUPPORT_TECHNICAL_URL', 'https://web.bale.ai'),
    'support_consultant_url' => env('SUPPORT_CONSULTANT_URL', 'https://web.bale.ai'),
    'course_checkout_url' => env('COURSE_CHECKOUT_URL', 'https://example.com/course'),
    'webinar_landing_url' => env('WEBINAR_LANDING_URL', 'https://example.com/webinar'),
];
