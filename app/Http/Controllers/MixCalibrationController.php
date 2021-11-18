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
    public function index(Request $request)
    {
        if (!$this->controlUser('mix_calibration', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('mix_calibration');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'mix_calibration', 'create')) {
            return Hermes::send('lng_0002', 401);
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
            'created_at' => Pariette::now()
        ];

        $work = DB::table('mix_calibration')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('mix_calibration')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('mix_calibration', 'update')) {
            return Hermes::send('lng_0002', 401);
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
    

    public function destroy($id)
    {
    }
}
