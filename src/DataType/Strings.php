<?php

namespace Rediscope\DataType;

use Illuminate\Support\Arr;

class Strings extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        $data = $this->getConnection()->get($key);

        return @unserialize($data) !== false ? unserialize($data) : $data;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $this->store($params);
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = Arr::get($params, 'key');
        $value = Arr::get($params, 'value');
        $seconds = Arr::get($params, 'seconds');

        $this->getConnection()->set($key, $value);

        if ($seconds > 0) {
            $this->getConnection()->expire($key, $seconds);
        }
    }
}
