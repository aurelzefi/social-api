<?php

namespace App\Http\Controllers;

class FilesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  string  $name
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke($name)
    {
        return response()->file(storage_path('app/'.$name));
    }
}
