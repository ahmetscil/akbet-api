<?php

namespace App\Actions;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetImage
{
    use AsAction;

    public function asController(Request $request)
    {
        return $this->handle($request);
    }
  
    public function handle($request)
    {
        $path = Storage::path($request->img);
        $url = Storage::url($request->img);

        return $path;
    }
}
