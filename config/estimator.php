<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Show estimate to customer
    |--------------------------------------------------------------------------
    |
    | The moving-quote estimate is calculated server-side for every lead but
    | is never shown to the public. This flag is a placeholder for turning
    | that on later without rewriting the calculation or the controllers:
    | when true, the public booking API is allowed to include the estimate
    | in its response. Keep this false until the pricing data has been
    | reviewed and confirmed to be trustworthy.
    |
    */
    'show_estimate_to_customer' => env('SHOW_ESTIMATE_TO_CUSTOMER', false),
];
