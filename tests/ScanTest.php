<?php

namespace Rediscope\Tests;

use Illuminate\Support\Facades\Redis;
use Rediscope\Rediscope;

class ScanTest extends FeatureTestCase
{
    protected function tearDown(): void
    {
        Redis::connection()->del('rediscope_scan_test_string');
        parent::tearDown();
    }

    public function test_scan_reports_the_real_type_of_each_key()
    {
        Redis::connection()->set('rediscope_scan_test_string', 'hello');

        $entries = Rediscope::instance('default')->scan('rediscope_scan_test_string');

        $entry = $entries->first(function ($entry) {
            return str_ends_with($entry['key'], 'rediscope_scan_test_string');
        });

        $this->assertNotNull($entry, 'Expected the scanned key to be present in the results.');
        $this->assertSame('string', $entry['type']);
    }
}
