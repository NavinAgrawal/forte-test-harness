<?php

namespace ForteTestHarness\Tests;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('FORTE_CONFIG_PATH=' . __DIR__ . '/fixtures/config.test.php');
        if (function_exists('forte_config_reset')) {
            forte_config_reset();
        }
        $GLOBALS['base_url'] = '';
        $GLOBALS['js_url'] = '';
        $GLOBALS['pg_action_url'] = '';
        unset($GLOBALS['organization_id'], $GLOBALS['location_id']);
    }

    protected function tearDown(): void
    {
        $vars = [
            'FORTE_API_ACCESS_ID',
            'FORTE_ENV',
            'FORTE_BASE_URL',
            'FORTE_BASE_URL_PRODUCTION',
            'FORTE_BASE_URL_SANDBOX',
            'FORTE_JS_URL',
            'FORTE_JS_URL_PRODUCTION',
            'FORTE_JS_URL_SANDBOX',
            'FORTE_PG_ACTION_URL',
            'FORTE_PG_ACTION_URL_PRODUCTION',
            'FORTE_PG_ACTION_URL_SANDBOX',
            'FORTE_SWP_BASE_URL',
            'FORTE_SWP_BASE_URL_PRODUCTION',
            'FORTE_SWP_BASE_URL_SANDBOX',
            'HTML2PDF_API_KEY',
        ];

        foreach ($vars as $var) {
            putenv($var);
        }

        $_POST = [];

        if (function_exists('forte_config_reset')) {
            forte_config_reset();
        }
    }

    public function testConfigLoadsFromEnvPath(): void
    {
        $this->assertSame('test_access', forte_config('api_access_id'));
        $this->assertSame('test_secure', forte_config('api_secure_key'));
        $this->assertSame('org_test', forte_config('organization_id'));
        $this->assertSame('loc_test', forte_config('location_id'));
        $this->assertSame('test_html2pdf_key', forte_config('html2pdf_api_key'));
    }

    public function testEnvOverridesConfigValues(): void
    {
        putenv('FORTE_API_ACCESS_ID=env_access');
        forte_config_reset();

        $this->assertSame('env_access', forte_config('api_access_id'));
    }

    public function testHtml2PdfApiKeyEnvOverride(): void
    {
        putenv('HTML2PDF_API_KEY=env_html2pdf');
        forte_config_reset();

        $this->assertSame('env_html2pdf', forte_config('html2pdf_api_key'));
    }

    public function testConfigReturnsDefaultWhenMissing(): void
    {
        $this->assertSame('fallback', forte_config('missing_key', 'fallback'));
    }

    public function testGlobalsAreApplied(): void
    {
        forte_config_apply_globals();
        $this->assertSame('https://example.test/v3', $GLOBALS['base_url']);
        $this->assertSame('https://example.test/js/v1', $GLOBALS['js_url']);
        $this->assertSame('https://example.test/postauth.pl', $GLOBALS['pg_action_url']);
        $this->assertSame('org_test', $GLOBALS['organization_id']);
        $this->assertSame('loc_test', $GLOBALS['location_id']);
    }

    public function testGlobalsPopulateWhenUnset(): void
    {
        $GLOBALS['base_url'] = '';
        $GLOBALS['js_url'] = '';
        $GLOBALS['pg_action_url'] = '';
        unset($GLOBALS['organization_id']);
        unset($GLOBALS['location_id']);

        forte_config_apply_globals();

        $this->assertSame('https://example.test/v3', $GLOBALS['base_url']);
        $this->assertSame('https://example.test/js/v1', $GLOBALS['js_url']);
        $this->assertSame('https://example.test/postauth.pl', $GLOBALS['pg_action_url']);
        $this->assertSame('org_test', $GLOBALS['organization_id']);
        $this->assertSame('loc_test', $GLOBALS['location_id']);
    }

    public function testBaseUrlUsesEnvOverride(): void
    {
        putenv('FORTE_BASE_URL=https://override.test/v3');
        forte_config_reset();

        $this->assertSame('https://override.test/v3', forte_base_url());
    }

    public function testBaseUrlUsesConfigOverride(): void
    {
        $tmp_path = __DIR__ . '/fixtures/config.baseurl.php';
        file_put_contents($tmp_path, "<?php\nreturn ['base_url' => 'https://config.test/v3'];\n");

        putenv('FORTE_CONFIG_PATH=' . $tmp_path);
        forte_config_reset();

        $this->assertSame('https://config.test/v3', forte_base_url());

        unlink($tmp_path);
        putenv('FORTE_CONFIG_PATH=' . __DIR__ . '/fixtures/config.test.php');
        forte_config_reset();
    }

    public function testBaseUrlUsesSandboxWhenEnvIsSandbox(): void
    {
        putenv('FORTE_ENV=sandbox');
        forte_config_reset();

        $this->assertSame('https://sandbox.example.test/v3', forte_base_url());
    }

    public function testBaseUrlUsesEnvironmentFromConfig(): void
    {
        $tmp_path = __DIR__ . '/fixtures/config.env.php';
        file_put_contents(
            $tmp_path,
            "<?php\nreturn ['environment' => 'sandbox', 'base_url_sandbox' => 'https://cfg-sandbox.test/v3'];\n"
        );

        putenv('FORTE_CONFIG_PATH=' . $tmp_path);
        putenv('FORTE_ENV');
        forte_config_reset();

        $this->assertSame('https://cfg-sandbox.test/v3', forte_base_url());

        unlink($tmp_path);
        putenv('FORTE_CONFIG_PATH=' . __DIR__ . '/fixtures/config.test.php');
        forte_config_reset();
    }

    public function testJsUrlUsesEnvOverride(): void
    {
        putenv('FORTE_JS_URL=https://override.test/js/v1');
        forte_config_reset();

        $this->assertSame('https://override.test/js/v1', forte_js_url());
    }

    public function testPgActionUrlUsesEnvOverride(): void
    {
        putenv('FORTE_PG_ACTION_URL=https://override.test/postauth.pl');
        forte_config_reset();

        $this->assertSame('https://override.test/postauth.pl', forte_pg_action_url());
    }

    public function testSwpBaseUrlUsesEnvOverride(): void
    {
        putenv('FORTE_SWP_BASE_URL=https://override.test');
        forte_config_reset();

        $this->assertSame('https://override.test', forte_swp_base_url());
    }

    public function testSwpUrlBuildsPath(): void
    {
        $this->assertSame('https://swp.example.test/co/default.aspx', forte_swp_url('co/default.aspx'));
        $this->assertSame('https://swp.example.test', forte_swp_url(''));
    }

    public function testPostValueUsesPost(): void
    {
        $_POST['api_access_id'] = 'posted_access';

        $this->assertSame('posted_access', forte_post_value('api_access_id', 'api_access_id'));
    }

    public function testPostValueFallsBackToConfig(): void
    {
        unset($_POST['api_access_id']);

        $this->assertSame('test_access', forte_post_value('api_access_id', 'api_access_id'));
    }

    public function testPrefixedPostAddsPrefix(): void
    {
        $_POST['organization_id'] = '12345';

        $this->assertSame('org_12345', forte_prefixed_post('organization_id', 'org_', 'organization_id'));
    }

    public function testPrefixedPostUsesConfigWhenMissing(): void
    {
        unset($_POST['organization_id']);

        $this->assertSame('org_test', forte_prefixed_post('organization_id', 'org_', 'organization_id'));
    }

    public function testPrefixedPostReturnsEmptyWhenConfigEmpty(): void
    {
        $tmp_path = __DIR__ . '/fixtures/config.empty.php';
        file_put_contents($tmp_path, "<?php\nreturn ['organization_id' => ''];\n");

        putenv('FORTE_CONFIG_PATH=' . $tmp_path);
        forte_config_reset();
        unset($_POST['organization_id']);

        $this->assertSame('', forte_prefixed_post('organization_id', 'org_', 'organization_id'));

        unlink($tmp_path);
        putenv('FORTE_CONFIG_PATH=' . __DIR__ . '/fixtures/config.test.php');
        forte_config_reset();
    }

    public function testConfigUsesLocalFileWhenPresent(): void
    {
        $local_path = __DIR__ . '/../../api-demo-php-harness/config/config.local.php';
        $local_contents = "<?php\n\nreturn ['api_access_id' => 'local_access'];\n";
        file_put_contents($local_path, $local_contents);

        putenv('FORTE_CONFIG_PATH');
        forte_config_reset();

        $this->assertSame('local_access', forte_config('api_access_id'));

        unlink($local_path);
        forte_config_reset();
    }

    public function testConfigFallsBackToExampleWhenNoOverrides(): void
    {
        putenv('FORTE_CONFIG_PATH');
        forte_config_reset();

        $this->assertSame('YOUR_API_ACCESS_ID', forte_config('api_access_id'));
    }

    public function testConfigHandlesNonArraySource(): void
    {
        $tmp_path = __DIR__ . '/fixtures/config.invalid.php';
        file_put_contents($tmp_path, "<?php\nreturn 'invalid';\n");

        putenv('FORTE_CONFIG_PATH=' . $tmp_path);
        forte_config_reset();

        $this->assertSame('fallback', forte_config('missing_key', 'fallback'));

        unlink($tmp_path);
        forte_config_reset();
    }

    public function testConfigReturnsDefaultWhenKeyIsEmpty(): void
    {
        $this->assertSame('empty', forte_config('', 'empty'));
    }
}
