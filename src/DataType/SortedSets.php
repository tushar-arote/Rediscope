<?php

namespace Rediscope\DataType;

use Illuminate\Support\Arr;

class SortedSets extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->zrange($key, 0, -1, ['WITHSCORES' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = Arr::get($params, 'key');
        $member = Arr::get($params, 'member');
        $action = Arr::get($params, 'action');

        if ($action === 'zrem') {
            $this->getConnection()->zrem($key, $member);
        }

        if ($action === 'zset') {
            $score = Arr::get($params, 'score');
            $this->getConnection()->zadd($key, [$member => $score]);
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

        $fields = [];

        foreach ($members as $member) {
            $fields[$member['member']] = $member['score'];
        }

        $this->getConnection()->zadd($key, $fields);

        if ($expire > 0) {
            $this->getConnection()->expire($key, $expire);
        }
    }

    /**
     * Remove a member from a sorted set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = Arr::get($params, 'key');
        $member = Arr::get($params, 'member');

        return $this->getConnection()->zrem($key, $member);
    }
}
