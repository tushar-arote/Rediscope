<?php

namespace Rediscope\Tests;

use Illuminate\Support\Facades\Redis;
use Rediscope\Rediscope;

class InstanceTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->get('config')->set('database.redis.secondary', [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => null,
            'port' => env('REDIS_PORT', 6379),
            'database' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        Redis::connection('default')->del('instance_test_default_key');
        Redis::connection('secondary')->del('instance_test_secondary_key');
        parent::tearDown();
    }

    public function test_instance_switches_connection_on_each_call()
    {
        Redis::connection('default')->set('instance_test_default_key', 'a');
        Redis::connection('secondary')->set('instance_test_secondary_key', 'b');

        $defaultEntries = Rediscope::instance('default')->scan('instance_test_default_key');
        $secondaryEntries = Rediscope::instance('secondary')->scan('instance_test_secondary_key');

        $this->assertTrue(
            $defaultEntries->contains(fn ($entry) => str_ends_with($entry['key'], 'instance_test_default_key')),
            'Expected the default connection to see its own key.'
        );
        $this->assertTrue(
            $secondaryEntries->contains(fn ($entry) => str_ends_with($entry['key'], 'instance_test_secondary_key')),
            'Expected the secondary connection to see its own key after switching connections.'
        );
    }
}
