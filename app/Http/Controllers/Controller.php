<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Store the files for the specified model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    protected function storeFilesFor(Model $model)
    {
        if (request()->hasFile('files')) {
            return $model->files()->createMany(
                collect(request()->file('files'))->map(function ($file) {
                    return ['name' => $file->store('/')];
                })->toArray()
            );
        }
    }
}
