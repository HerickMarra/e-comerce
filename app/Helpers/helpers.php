<?php

if (!function_exists('app_setting')) {
    /**
     * Get a cached application setting.
     *
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    function app_setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}
