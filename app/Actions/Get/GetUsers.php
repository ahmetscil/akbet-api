<?php

namespace App\Actions\Get;

use Lorisleiva\Actions\Concerns\AsAction;

class GetUsers
{
    use AsAction;

    public function asController(Request $request)
    {
        return $this->handle($request);
    }
  
    public function handle($request)
    {

        $query = DB::table('users');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('Not Found', 404);
    }
}
