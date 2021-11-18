<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MeasurementController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser('measurement', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('measurement');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'measurement', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'mix' => 'required',
            'sensor' => 'required',
            'max_temp' => 'required',
            'min_temp' => 'required',
            'last_temp' => 'required',
            'readed_max' => 'required',
            'readed_min' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
            'deployed_at' => 'required',
            'last_data_at' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'mix' => $request->mix,
            'sensor' => $request->sensor,
            'max_temp' => $request->max_temp,
            'min_temp' => $request->min_temp,
            'last_temp' => $request->last_temp,
            'readed_max' => $request->readed_max,
            'readed_min' => $request->readed_min,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'deployed_at' => $request->deployed_at,
            'last_data_at' => $request->last_data_at,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('measurement')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('measurement')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('measurement', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'mix' => 'required',
            'sensor' => 'required',
            'max_temp' => 'required',
            'min_temp' => 'required',
            'last_temp' => 'required',
            'readed_max' => 'required',
            'readed_min' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
            'deployed_at' => 'required',
            'last_data_at' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'mix' => $request->mix,
            'sensor' => $request->sensor,
            'max_temp' => $request->max_temp,
            'min_temp' => $request->min_temp,
            'last_temp' => $request->last_temp,
            'readed_max' => $request->readed_max,
            'readed_min' => $request->readed_min,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'deployed_at' => $request->deployed_at,
            'last_data_at' => $request->last_data_at,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('measurement')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
