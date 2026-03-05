<?php

namespace Rediscope\DataType;

use Illuminate\Support\Arr;

class Sets extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->smembers($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = Arr::get($params, 'key');
        $member = Arr::get($params, 'member');
        $action = Arr::get($params, 'action');

        if ($action === 'srem') {
            $this->getConnection()->srem($key, $member);
        }

        if ($action === 'sadd') {
            $this->getConnection()->sadd($key, [$member]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = Arr::get($params, 'key');
        $members = Arr::get($params, 'members');
        $seconds = Arr::get($params, 'seconds');

        $this->getConnection()->sadd($key, $members);

        if ($seconds > 0) {
            $this->getConnection()->expire($key, $seconds);
        } else {
            $this->getConnection()->persist($key);
        }
    }

    /**
     * Remove a member from a set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = Arr::get($params, 'key');
        $member = Arr::get($params, 'member');

        return $this->getConnection()->srem($key, $member);
    }
}
