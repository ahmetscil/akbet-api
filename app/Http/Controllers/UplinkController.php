<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UplinkController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser('uplink', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('uplink');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'uplink', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        $validator = Validator::make($request->all(), [
            'measurement' => 'required',
            'DevEUI' => 'required',
            'payload_hex' => 'required',
            'LrrRSSI' => 'required',
            'LrrSNR' => 'required',
            'temperature' => 'required',
            'maturity' => 'required',
            'strength' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'measurement' => $request->measurement,
            'DevEUI' => $request->DevEUI,
            'payload_hex' => $request->payload_hex,
            'LrrRSSI' => $request->LrrRSSI,
            'LrrSNR' => $request->LrrSNR,
            'temperature' => $request->temperature,
            'maturity' => $request->maturity,
            'strength' => $request->strength,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('uplink')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('uplink')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('uplink', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'measurement' => 'required',
            'DevEUI' => 'required',
            'payload_hex' => 'required',
            'LrrRSSI' => 'required',
            'LrrSNR' => 'required',
            'temperature' => 'required',
            'maturity' => 'required',
            'strength' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'measurement' => $request->measurement,
            'DevEUI' => $request->DevEUI,
            'payload_hex' => $request->payload_hex,
            'LrrRSSI' => $request->LrrRSSI,
            'LrrSNR' => $request->LrrSNR,
            'temperature' => $request->temperature,
            'maturity' => $request->maturity,
            'strength' => $request->strength,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('uplink')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
