<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LookupItemController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser($request->store, 'lookup', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('lookup_item');

        if ($request->lookup) {
            $query->where('lookup_item.lookup', $request->lookup);
        }
        if ($request->key) {
            $query->where('lookup_item.key', $request->key);
        }
        if ($request->value) {
            $query->where('lookup_item.value', $request->value);
        }

        $query->join('lookup', 'lookup_item.lookup', 'lookup.id');
        $query->select('lookup_item.*', 'lookup.type as lookupName');
        $data = $query->orderBy('lookup_item.id', 'DESC')->get();

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
            'lookup' => $request->lookup,
            'key' => $request->key,
            'value' => $request->value,
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
        $data = DB::table('lookup')->find($id);
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
