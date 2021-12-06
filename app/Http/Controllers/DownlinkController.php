<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DownlinkController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser($request->store, 'downlink', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('downlink');

        if ($request->measurement) {
            $query->where('measurement', $request->measurement);
        }
        if ($request->DevEUI) {
            $query->where('DevEUI', $request->DevEUI);
        }
        if ($request->payload_hex) {
            $query->where('payload_hex', $request->payload_hex);
        }

        $query->join('measurement','measurement.id','=','downlink.measurement');
        $query->select('downlink.*', 'measurement.name as measurement');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'downlink', 'create')) {
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
            'measurement' => $request->measurement,
            'DevEUI' => $request->DevEUI,
            'payload_hex' => $request->payload_hex,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('downlink')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('downlink')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('downlink', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'DevEUI' => 'required',
            'payload_hex' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'measurement' => $request->measurement,
            'DevEUI' => $request->DevEUI,
            'payload_hex' => $request->payload_hex,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('downlink')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
