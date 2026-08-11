<?php

namespace Rediscope\Tests;

use Rediscope\RediscopeServiceProvider;

class PublishesTest extends FeatureTestCase
{
    public function test_config_file_is_registered_for_vendor_publish()
    {
        $paths = RediscopeServiceProvider::pathsToPublish(
            RediscopeServiceProvider::class,
            'rediscope-config'
        );

        $configSource = realpath(__DIR__.'/../config/rediscope.php');
        $normalized = [];
        foreach ($paths as $from => $to) {
            $normalized[realpath($from)] = $to;
        }

        $this->assertArrayHasKey($configSource, $normalized);
        $this->assertSame(config_path('rediscope.php'), $normalized[$configSource]);
    }
}
