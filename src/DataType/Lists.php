<?php

namespace Rediscope\DataType;

use Illuminate\Support\Arr;

class Lists extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->lrange($key, 0, -1);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = Arr::get($params, 'key');

        $action = Arr::get($params, 'action');

        if (in_array($action, ['lpush', 'rpush'])) {
            $members = Arr::get($params, 'members');
            $this->getConnection()->{$action}($key, $members);
        }

        if ($action == 'lset') {
            $value = Arr::get($params, 'value');
            $index = Arr::get($params, 'index');

            $this->getConnection()->lset($key, $index, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = Arr::get($params, 'key');
        $members = Arr::get($params, 'members');
        $expire = Arr::get($params, 'expire');
        $action = Arr::get($params, 'action', 'rpush');

        $members = array_column($members, 'value');

        $this->getConnection()->{$action}($key, $members);

        if ($expire > 0) {
            $this->getConnection()->expire($key, $expire);
        }
    }

    /**
     * Remove a member from list by index.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function remove(array $params)
    {
        $key = Arr::get($params, 'key');
        $index = Arr::get($params, 'index');

        $lua = <<<'LUA'
redis.call('lset', KEYS[1], ARGV[1], '__DELETED__');
redis.call('lrem', KEYS[1], 1, '__DELETED__');
LUA;

        return $this->getConnection()->eval($lua, 1, $key, $index);
    }
}
