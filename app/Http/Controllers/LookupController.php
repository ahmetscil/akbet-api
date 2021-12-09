<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LookupController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser($request->store, 'lookup', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('lookup');

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $data = $query->orderBy('id', 'DESC')->get();
        foreach ($data as $d) {
            $d->items = DB::table('lookup_item')->where('lookup', $d->id)->orderBy('id', 'DESC')->get();
        }

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'lookup', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        // $validator = Validator::make($request->all(), [
        //     'DevEUI' => 'required',
        //     'payload_hex' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

        $data = [
            'type' => $request->type,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('lookup')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $lookup = DB::table('lookup')->where('type', $id)->first();
        if ($lookup) {
            $lookup->items = DB::table('lookup_item')->where('lookup', $lookup->id)->get();
        }

        $data = $lookup;

        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        // if (!$this->controlUser('lookup', 'update')) {
        //     return Hermes::send('lng_0002', 401);
        // }
        // $validator = Validator::make($request->all(), [
        //     'DevEUI' => 'required',
        //     'payload_hex' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }
    
        $data = [
            'type' => $request->type,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('lookup')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
