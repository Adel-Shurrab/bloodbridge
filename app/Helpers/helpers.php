<?php

if (!function_exists('app_constant')) {
    /**
     * Get a constant value from the constants array.
     * 
     * @param string $key Dot notation key (e.g., 'user.roles.DONOR' or 'blood_types_list')
     * @param mixed $default Default value if key not found
     * @return mixed
     * 
     * @example
     * app_constant('user.roles.DONOR') // Returns 'donor'
     * app_constant('blood_types_list') // Returns ['O+', 'O-', ...]
     * app_constant('blood_request.status.PENDING') // Returns 'pending'
     */
    function app_constant(string $key, mixed $default = null): mixed
    {
        static $constants = null;
        if ($constants === null) {
            $constants = require app_path('Helpers/constant.php');
        }

        $keys = explode('.', $key);
        $value = $constants;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('constants')) {
    /**
     * Get all constants or a specific group.
     * 
     * @param string|null $group Optional group name (e.g., 'user', 'blood_request')
     * @return array
     * 
     * @example
     * constants() // Returns all constants
     * constants('user') // Returns user constants
     * constants('blood_request.status') // Returns status constants
     */
    function constants(?string $group = null): array
    {
        static $constants = null;
        if ($constants === null) {
            $constants = require app_path('Helpers/constant.php');
        }

        if ($group === null) {
            return $constants;
        }

        $keys = explode('.', $group);
        $value = $constants;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return [];
            }
            $value = $value[$segment];
        }

        return is_array($value) ? $value : [];
    }
}

if (!function_exists('trans_const')) {
    /**
     * Get translated constant value using Laravel's translation system.
     * 
     * @param string $key Translation key (e.g., 'constants.blood_request_status.pending')
     * @param array $replace Replacements for the translation
     * @param string|null $locale Locale code (default: null uses current locale)
     * @return string
     * 
     * @example
     * trans_const('blood_request_status.pending') // Returns 'قيد الانتظار' (if locale is 'ar')
     * trans_const('gender.male') // Returns 'ذكر'
     * trans_const('user_roles.donor') // Returns 'متبرع'
     */
    function trans_const(string $key, array $replace = [], ?string $locale = null): string
    {
        if (str_starts_with($key, 'constants.')) {
            return __($key, $replace, $locale);
        }

        return __('constants.' . $key, $replace, $locale);
    }
}
