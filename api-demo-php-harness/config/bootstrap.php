<?php

if (!function_exists('forte_env_value')) {
    function forte_env_value(string $key, $default = null) {
        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }
        return $value;
    }
}

if (!function_exists('forte_config')) {
    function forte_config(string $key, $default = null) {
        if (!array_key_exists('__forte_config_cache', $GLOBALS) || $GLOBALS['__forte_config_cache'] === null) {
            $env_path = getenv('FORTE_CONFIG_PATH');
            if ($env_path && is_file($env_path)) {
                $config = require $env_path;
            } else {
                $local_path = __DIR__ . '/config.local.php';
                if (is_file($local_path)) {
                    $config = require $local_path;
                } else {
                    $config = require __DIR__ . '/config.example.php';
                }
            }

            if (!is_array($config)) {
                $config = [];
            }

            $env_overrides = [
                'environment' => 'FORTE_ENV',
                'base_url' => 'FORTE_BASE_URL',
                'base_url_production' => 'FORTE_BASE_URL_PRODUCTION',
                'base_url_sandbox' => 'FORTE_BASE_URL_SANDBOX',
                'js_url' => 'FORTE_JS_URL',
                'js_url_production' => 'FORTE_JS_URL_PRODUCTION',
                'js_url_sandbox' => 'FORTE_JS_URL_SANDBOX',
                'pg_action_url' => 'FORTE_PG_ACTION_URL',
                'pg_action_url_production' => 'FORTE_PG_ACTION_URL_PRODUCTION',
                'pg_action_url_sandbox' => 'FORTE_PG_ACTION_URL_SANDBOX',
                'swp_base_url' => 'FORTE_SWP_BASE_URL',
                'swp_base_url_production' => 'FORTE_SWP_BASE_URL_PRODUCTION',
                'swp_base_url_sandbox' => 'FORTE_SWP_BASE_URL_SANDBOX',
                'organization_id' => 'FORTE_ORGANIZATION_ID',
                'location_id' => 'FORTE_LOCATION_ID',
                'api_access_id' => 'FORTE_API_ACCESS_ID',
                'api_secure_key' => 'FORTE_API_SECURE_KEY',
                'api_login_id' => 'FORTE_API_LOGIN_ID',
                'secure_transaction_key' => 'FORTE_SECURE_TRANSACTION_KEY',
                'pg_password' => 'FORTE_PG_PASSWORD',
                'pg_merchant_id' => 'FORTE_PG_MERCHANT_ID',
                'pg_payment_token' => 'FORTE_PG_PAYMENT_TOKEN',
                'pg_customer_token' => 'FORTE_PG_CUSTOMER_TOKEN',
                'freshdesk_api_key' => 'FRESHDESK_API_KEY',
                'freshdesk_domain' => 'FRESHDESK_DOMAIN',
                'freshdesk_password' => 'FRESHDESK_PASSWORD',
                'html2pdf_api_key' => 'HTML2PDF_API_KEY',
            ];

            foreach ($env_overrides as $config_key => $env_key) {
                $value = forte_env_value($env_key);
                if ($value !== null) {
                    $config[$config_key] = $value;
                }
            }

            $GLOBALS['__forte_config_cache'] = $config;
        }

        $config = $GLOBALS['__forte_config_cache'];

        if ($key === '') {
            return $default;
        }

        if (array_key_exists($key, $config)) {
            return $config[$key];
        }

        return $default;
    }
}

if (!function_exists('forte_config_reset')) {
    function forte_config_reset(): void {
        $GLOBALS['__forte_config_cache'] = null;
    }
}

if (!function_exists('forte_env_name')) {
    function forte_env_name(): string {
        $env = forte_env_value('FORTE_ENV', null);
        if ($env === null) {
            $env = forte_config('environment', 'production');
        }
        return strtolower((string)$env);
    }
}

if (!function_exists('forte_select_url')) {
    function forte_select_url(
        string $env_override,
        string $config_override,
        string $config_prod,
        string $config_sandbox,
        string $default_prod,
        string $default_sandbox
    ): string {
        $override = forte_env_value($env_override, null);
        if ($override !== null) {
            return $override;
        }

        $config_value = forte_config($config_override, '');
        if ($config_value !== '') {
            return $config_value;
        }

        if (forte_env_name() === 'sandbox') {
            return (string)forte_config($config_sandbox, $default_sandbox);
        }

        return (string)forte_config($config_prod, $default_prod);
    }
}

if (!function_exists('forte_base_url')) {
    function forte_base_url(): string {
        return forte_select_url(
            'FORTE_BASE_URL',
            'base_url',
            'base_url_production',
            'base_url_sandbox',
            'https://api.forte.net/v3',
            'https://sandbox.forte.net/api/v3'
        );
    }
}

if (!function_exists('forte_js_url')) {
    function forte_js_url(): string {
        return forte_select_url(
            'FORTE_JS_URL',
            'js_url',
            'js_url_production',
            'js_url_sandbox',
            'https://api.forte.net/js/v1',
            'https://sandbox.forte.net/api/js/v1'
        );
    }
}

if (!function_exists('forte_pg_action_url')) {
    function forte_pg_action_url(): string {
        return forte_select_url(
            'FORTE_PG_ACTION_URL',
            'pg_action_url',
            'pg_action_url_production',
            'pg_action_url_sandbox',
            'https://www.paymentsgateway.net/cgi-bin/postauth.pl',
            'https://www.paymentsgateway.net/cgi-bin/posttest.pl'
        );
    }
}

if (!function_exists('forte_swp_base_url')) {
    function forte_swp_base_url(): string {
        return forte_select_url(
            'FORTE_SWP_BASE_URL',
            'swp_base_url',
            'swp_base_url_production',
            'swp_base_url_sandbox',
            'https://swp.paymentsgateway.net',
            'https://sandbox.paymentsgateway.net'
        );
    }
}

if (!function_exists('forte_swp_url')) {
    function forte_swp_url(string $path): string {
        $base = rtrim(forte_swp_base_url(), '/');
        $path = ltrim($path, '/');
        if ($path === '') {
            return $base;
        }
        return $base . '/' . $path;
    }
}

if (!function_exists('forte_post_value')) {
    function forte_post_value(string $post_key, string $config_key): string {
        if (isset($_POST[$post_key]) && $_POST[$post_key] !== '') {
            return (string)$_POST[$post_key];
        }
        return (string)forte_config($config_key);
    }
}

if (!function_exists('forte_prefixed_post')) {
    function forte_prefixed_post(string $post_key, string $prefix, string $config_key): string {
        $value = forte_post_value($post_key, $config_key);
        if ($value === '') {
            return '';
        }
        if (strpos($value, $prefix) !== 0) {
            $value = $prefix . $value;
        }
        return $value;
    }
}

if (!function_exists('forte_config_apply_globals')) {
    function forte_config_apply_globals(): void {
        $map = [
            'organization_id' => 'organization_id',
            'location_id' => 'location_id',
            'api_access_id' => 'api_access_id',
            'api_secure_key' => 'api_secure_key',
            'api_login_id' => 'api_login_id',
            'secure_transaction_key' => 'secure_transaction_key',
            'pg_password' => 'pg_password',
            'pg_merchant_id' => 'pg_merchant_id',
            'pg_payment_token' => 'pg_payment_token',
            'pg_customer_token' => 'pg_customer_token',
        ];

        if (!isset($GLOBALS['base_url']) || $GLOBALS['base_url'] === '') {
            $GLOBALS['base_url'] = forte_base_url();
        }
        if (!isset($GLOBALS['js_url']) || $GLOBALS['js_url'] === '') {
            $GLOBALS['js_url'] = forte_js_url();
        }
        if (!isset($GLOBALS['pg_action_url']) || $GLOBALS['pg_action_url'] === '') {
            $GLOBALS['pg_action_url'] = forte_pg_action_url();
        }

        foreach ($map as $var => $key) {
            if (!isset($GLOBALS[$var]) || $GLOBALS[$var] === '') {
                $GLOBALS[$var] = forte_config($key);
            }
        }
    }
}

forte_config_apply_globals();
