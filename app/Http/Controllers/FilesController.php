<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;

class FilesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $files
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Filesystem $files, $name)
    {
        return response()->file($files->get($name));
    }
}
