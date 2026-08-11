<?php

namespace Rediscope;


use Rediscope\DataType\DataType;
use Rediscope\DataType\Hashes;
use Rediscope\DataType\Lists;
use Rediscope\DataType\Sets;
use Rediscope\DataType\SortedSets;
use Rediscope\DataType\Strings;
use Rediscope\Formatter\Information;
use Illuminate\Http\Request;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Predis\Collection\Iterator\Keyspace;
use Predis\Pipeline\Pipeline;

class Rediscope
{
    use AuthorizesRequests;

    /**
     * @var array
     */
    protected $dataTyps = [
        'string' => Strings::class,
        'hash' => Hashes::class,
        'set' => Sets::class,
        'zset' => SortedSets::class,
        'list' => Lists::class,
    ];

    /**
     * @var Rediscope
     */
    protected static $instance;

    /**
     * @var string
     */
    protected $connection;

    /**
     * Indicates if Telescope should use the dark theme.
     *
     * @var bool
     */
    public static $useDarkTheme = false;


    /**
     * Rediscope constructor.
     *
     * @param string $connection
     */
    public function __construct($connection = 'default')
    {
        Log::info($connection);
        $this->connection = $connection;
    }

    /**
     * Get instance of redis manager.
     *
     * @param string $connection
     *
     * @return Rediscope
     */
    public static function instance($connection = 'default')
    {
        if (!static::$instance instanceof self) {
            static::$instance = new static($connection);
        }

        static::$instance->connection = $connection;

        return static::$instance;
    }

    /**
     * @return Lists
     */
    public function list()
    {
        return new Lists($this->getConnection());
    }

    /**
     * @return Strings
     */
    public function string()
    {
        return new Strings($this->getConnection());
    }

    /**
     * @return Hashes
     */
    public function hash()
    {
        return new Hashes($this->getConnection());
    }

    /**
     * @return Sets
     */
    public function set()
    {
        return new Sets($this->getConnection());
    }

    /**
     * @return SortedSets
     */
    public function zset()
    {
        return new SortedSets($this->getConnection());
    }

    /**
     * Get connection collections.
     *
     * @return Collection
     */
    public function getConnections()
    {
        return collect(config('database.redis'))->except(['options', 'clusters'])->filter(function ($conn) {
            return is_array($conn);
        });
    }

    /**
     * Get a registered connection instance.
     *
     * @param string $connection
     *
     * @return Connection
     */
    public function getConnection($connection = null)
    {
        if ($connection) {
            $this->connection = $connection;
        }

        return Redis::connection($this->connection);
    }

    /**
     * Get information of redis instance.
     *
     * @param mixed $section
     *
     * @return array
     */
    public function getInformation($section = null)
    {
        if ($section) {
            $info = $this->getConnection()->info($section);

            return Information::$section($info);
        }

        return $this->getConnection()->info();
    }

    /**
     * Scan keys in redis by giving pattern.
     *
     * @param string $pattern
     * @param int $count
     *
     * @return array|Collection|Pipeline
     */
    public function scan($pattern = '*', $count = 100)
    {
        $client = $this->getConnection();
        $rawClient = $client->client();
        $keys = [];

        $pattern = '*'.$pattern.'*';
        foreach (new Keyspace($rawClient, $pattern) as $item) {
            $keys[] = $item;

            if (count($keys) == $count) {
                break;
            }
        }
        //$keys = iterator_to_array(new Keyspace($rawClient, $pattern));

        // Keys from the Keyspace iterator already include the connection's
        // key prefix (if any), since it reads directly from Redis. Passing
        // them to eval() as-is would have the prefix applied a second time,
        // since Predis also prefixes EVAL's KEYS arguments - so it's
        // stripped here and let eval() re-apply it once.
        $prefix = optional($rawClient->getOptions()->prefix)->getPrefix() ?? '';

        // Static Lua script sent to Redis's own EVAL command via Predis - no
        // user input is interpolated into it, so this isn't an arbitrary
        // code execution risk in this PHP process.
        $script = <<<'LUA'
            local type = redis.call('type', KEYS[1])
            local ttl = redis.call('ttl', KEYS[1])
            local idletime = redis.call('object', 'idletime', KEYS[1])
            return {KEYS[1], type, ttl, idletime}
LUA;

        $keys = $rawClient->pipeline(function (Pipeline $pipe) use ($keys, $script, $prefix) {
            foreach ($keys as $key) {
                $unprefixedKey = $prefix && str_starts_with($key, $prefix)
                    ? substr($key, strlen($prefix))
                    : $key;

                $pipe->eval($script, 1, $unprefixedKey);
            }
        });

        return collect($keys)->map(function ($key) {
            return [
                'key' => $key[0],
                'type' => (string)$key[1],
                'ttl' => $this->getSecondsToTimeFormat($key[2]),
                'idletime' => $this->getSecondsToTimeFormat($key[3]),
            ];
        });
    }

    private function getSecondsToTimeFormat($seconds)
    {
        if ($seconds > 0) {
            $dtF = new \DateTime('@0');
            $dtT = new \DateTime("@$seconds");
            return $dtF->diff($dtT)->format('%a Days, %H:%I:%S');
        }

        return 'Forever';
    }

    /**
     * Fetch value of a giving key.
     *
     * @param string $key
     *
     * @return array
     */
    public function fetch($key)
    {
        if (!$this->getConnection()->exists($key)) {
            return [];
        }

        $type = $this->getConnection()->type($key)->__toString();

        /** @var DataType $class */
        $class = $this->{$type}();

        $value = $class->fetch($key);
        $expire = $this->getSecondsToTimeFormat($class->ttl($key));

        return compact('key', 'value', 'expire', 'type');
    }

    /**
     * Update a specified key.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function update(Request $request)
    {
        $key = $request->get('key');
        $type = $request->get('type');

        /** @var DataType $class */
        $class = $this->{$type}();

        $class->update($request->all());

        $class->setTtl($key, $request->get('ttl'));

        return true;
    }

    /**
     * Remove the specified key.
     *
     * @param array $keys
     *
     * @return int
     */
    public function del($keys)
    {
        return $this->getConnection()->del($keys);
    }

    /**
     * 运行redis命令.
     *
     * @param string $command
     * @param $db
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function execute($command, $db)
    {
        $command = explode(' ', trim($command));

        if ($this->commandDisabled($command[0])) {
            throw new \Exception("Command [{$command[0]}] is disabled!");
        }

        $client = $this->getConnection();

        if ($db !== null) {
            $client->select($db);
        }

        return $client->executeRaw($command);
    }

    /**
     * Determine if giving command is disabled.
     *
     * @param string $command
     *
     * @return bool
     */
    protected function commandDisabled(string $command)
    {
        $disabled = config('rediscope.disable_commands');

        $disabled = array_map('strtoupper', (array)$disabled);

        return in_array(strtoupper($command), $disabled);
    }

    /**
     * @param $key
     * @param int $seconds
     *
     * @return int
     */
    public function expire($key, $seconds = -1)
    {
        if ($seconds > 0) {
            return $this->getConnection()->expire($key, $seconds);
        } else {
            return $this->getConnection()->persist($key);
        }
    }

    /**
     * Specifies that Telescope should use the dark theme.
     *
     * @return static
     */
    public static function night()
    {
        static::$useDarkTheme = true;

        return new static;
    }

    /**
     * Get the default JavaScript variables for Telescope.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'path' => config('rediscope.path'),
            'timezone' => config('app.timezone'),
        ];
    }

    public static function status()
    {
        if (!config('rediscope.enabled', false)) {
            return 'disabled';
        }

        return 'enabled';
    }
}
