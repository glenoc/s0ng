<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class HttpsConfigurationTest extends TestCase
{
    public function test_force_https_uses_application_configuration_for_assets_and_routes(): void
    {
        URL::useAssetOrigin(null);
        config(['app.force_https' => true]);
        (new AppServiceProvider($this->app))->boot();

        try {
            $this->assertStringStartsWith('https://', asset('build/app.js'));
            $this->assertStringStartsWith('https://', route('login'));
        } finally {
            URL::forceScheme(null);
        }
    }
}
