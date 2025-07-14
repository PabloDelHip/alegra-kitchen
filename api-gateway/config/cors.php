<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | Specify the paths that should be accessible via CORS. You can use
    | wildcards like 'api/*' to allow all API routes.
    |
    */

    'paths' => ['api/*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | These are the HTTP methods that are allowed for CORS requests.
    | Use ['*'] to allow all standard methods.
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | These are the origins (domains) that are allowed to access the API.
    | Use ['*'] to allow all. For production, it's better to be explicit.
    |
    */

    'allowed_origins' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | You may also specify patterns that match origins using regex.
    |
    */

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | These headers are allowed in the request. Use ['*'] to allow any.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | These are the response headers that browsers are allowed to access.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | This defines how long the results of a preflight request can be cached.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Indicates whether the response to the request can be exposed when
    | credentials are present (like cookies or authorization headers).
    |
    */

    'supports_credentials' => false,

];
