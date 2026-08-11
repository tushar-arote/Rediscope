<?php

namespace Rediscope\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AssetController extends Controller
{
    /**
     * Known asset extensions and their Content-Type. Content-sniffing
     * (e.g. Symfony's MimeTypes) misidentifies CSS as text/plain, which
     * browsers refuse to apply as a stylesheet.
     *
     * @var array<string, string>
     */
    private const MIME_TYPES = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'map' => 'application/json',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];

    /**
     * Serve a compiled frontend asset directly from the package,
     * so consumers don't need a vendor:publish step.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! isset(self::MIME_TYPES[$extension])) {
            abort(404);
        }

        $publicPath = realpath(__DIR__.'/../../../public');
        $assetPath = realpath($publicPath.'/'.$path);

        if ($assetPath === false || ! str_starts_with($assetPath, $publicPath.DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        return new Response(file_get_contents($assetPath), 200, [
            'Content-Type' => self::MIME_TYPES[$extension],
        ]);
    }
}
