<?php

namespace Rediscope\Tests\Http;

use Rediscope\Tests\FeatureTestCase;

class AssetControllerTest extends FeatureTestCase
{
    public function test_it_serves_app_js_without_requiring_a_publish_step()
    {
        $response = $this->get('/vendor/rediscope/app.js');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/javascript');
        $this->assertSame(
            file_get_contents(__DIR__.'/../../public/app.js'),
            $response->getContent()
        );
    }

    public function test_it_serves_app_css_without_requiring_a_publish_step()
    {
        $response = $this->get('/vendor/rediscope/app.css');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/css; charset=utf-8');
        $this->assertSame(
            file_get_contents(__DIR__.'/../../public/app.css'),
            $response->getContent()
        );
    }

    public function test_it_returns_404_for_a_missing_asset()
    {
        $response = $this->get('/vendor/rediscope/does-not-exist.js');

        $response->assertStatus(404);
    }

    public function test_it_rejects_path_traversal_outside_the_public_directory()
    {
        $response = $this->get('/vendor/rediscope/'.urlencode('../composer.json'));

        $response->assertStatus(404);
    }
}
