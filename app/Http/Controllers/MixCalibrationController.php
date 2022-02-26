<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MixCalibrationController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('mix_calibration', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $query = DB::table('mix_calibration');

        if ($request->mix) {
            $query->where('mix', $request->mix);
        }
        if ($request->days) {
            $query->where('days', $request->days);
        }
        if ($request->strength) {
            $query->where('strength', $request->strength);
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('mix_calibration', 'create', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        // $validator = Validator::make($request->all(), [
        //     'mix' => 'required',
        //     'days' => 'required',
        //     'strength' => 'required',
        //     'status' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

        $data = [
            'mix' => $request->mix,
            'days' => $request->days,
            'strength' => $request->strength,
            'status' => $request->status ? $request->status : 1,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('mix_calibration')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $auth = Pariette::authRole('mix_calibration', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $data = DB::table('mix_calibration')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        $auth = Pariette::authRole('mix_calibration', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

		$validator = Validator::make($request->all(), [
            'mix' => 'required',
            'days' => 'required',
            'strength' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'mix' => $request->mix,
            'days' => $request->days,
            'strength' => $request->strength,
            'status' => $request->status,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('mix_calibration')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($storeToken, $id)
    {
        $auth = Pariette::authRole('mix_calibration', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
