<?php

namespace Rediscope\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Rediscope\Rediscope;


class RedisManagerController extends Controller
{
    /**
     * Get redis connections.
     *
     * @return Collection
     */
    public function connections()
    {
        $config = config('database.redis');

        return collect($config)->filter(function ($conn) {
            return is_array($conn);
        })->keys();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function scan(Request $request)
    {
        $manager = $this->manager();

        return response()->json([
            'entries' => $manager->scan(
                $request->get('pattern', '*'),
                $request->get('count', config('rediscope.results_per_page', 50))
            ),
            'status' => Rediscope::status()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function key(Request $request)
    {
        return response()->json([
            'entry' => $this->manager()->fetch($request->get('key'))
        ]);
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    public function destroy(Request $request)
    {
        return $this->manager()->del($request->get('keys'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        $section = $request->get('section');

        return response()->json([
            'entry' => $this->manager()->getInformation($section)
        ]);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $type = $request->get('type');

        return $this->manager()->{$type}()->store($request->all());
    }



    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function remove(Request $request)
    {
        $type = $request->get('type');

        return $this->manager()->{$type}()->remove($request->all());
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function update(Request $request)
    {
        return $this->manager()->update($request);
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    public function expire(Request $request)
    {
        return $this->manager()->expire($request->get('key'), $request->get('seconds'));
    }

    /**
     * Execute a redis command.
     *
     * @param Request $request
     *
     * @return array
     */
    public function eval(Request $request)
    {
        $command = $request->get('command');
        $db = $request->get('db');

        try {
            $result = $this->manager()->execute($command, $db);
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'data' => $exception->getMessage(),
                'command' => $command,
            ];
        }

        if (is_string($result) && Str::startsWith($result, ['ERR ', 'WRONGTYPE '])) {
            return [
                'success' => false,
                'data' => $result,
                'command' => $command,
            ];
        }

        return [
            'success' => true,
            'data' => $result,
            'command' => $command,
        ];
    }

    /**
     * Get the redis manager instance.
     *
     * @return Rediscope
     */
    protected function manager()
    {
        $conn = \request()->get('conn');

        if (empty($conn) || $conn === 'null') {
            $conn = 'default';
        }

        return Rediscope::instance($conn);
    }
}
