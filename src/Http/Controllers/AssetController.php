<?php

namespace Rediscope\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Symfony\Component\Mime\MimeTypes;

class AssetController extends Controller
{
    /**
     * Serve a compiled frontend asset directly from the package,
     * so consumers don't need a vendor:publish step.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $path)
    {
        $publicPath = realpath(__DIR__.'/../../../public');
        $assetPath = realpath($publicPath.'/'.$path);

        if ($assetPath === false || ! str_starts_with($assetPath, $publicPath)) {
            abort(404);
        }

        $mimeType = MimeTypes::getDefault()->guessMimeType($assetPath) ?? 'application/octet-stream';

        return new Response(file_get_contents($assetPath), 200, [
            'Content-Type' => $mimeType,
        ]);
    }
}
