<?php

namespace Rediscope\Tests;

use Rediscope\Rediscope;

class ConnectionsTest extends FeatureTestCase
{
    public function test_get_connections_excludes_the_reserved_options_and_clusters_keys()
    {
        $connections = Rediscope::instance()->getConnections()->keys();

        $this->assertTrue($connections->contains('default'));
        $this->assertTrue($connections->contains('cache'));
        $this->assertFalse($connections->contains('options'), 'The "options" key is Redis client config, not a connection.');
        $this->assertFalse($connections->contains('clusters'), 'The "clusters" key is Redis client config, not a connection.');
    }
}
