<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MeasurementController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        if (Pariette::authRole('measurement', 'read', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

        $query = DB::table('measurement');

        if ($request->name) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->description) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }
        if ($request->mix) {
            $query->where('mix', $request->mix);
        }
        if ($request->sensor) {
            $sensor = DB::table('sensors')->where('id', $request->sensor)->first();
            $query->where('sensor', $sensor->id);
        }
        if ($request->max_temp) {
            $query->where('max_temp', $request->max_temp);
        }
        if ($request->min_temp) {
            $query->where('min_temp', $request->min_temp);
        }
        if ($request->last_temp) {
            $query->where('last_temp', $request->last_temp);
        }
        if ($request->readed_max) {
            $query->where('readed_max', $request->readed_max);
        }
        if ($request->readed_min) {
            $query->where('readed_min', $request->readed_min);
        }
        if ($request->started_at) {
            $query->where('started_at', $request->started_at);
        }
        if ($request->ended_at) {
            $query->where('ended_at', $request->ended_at);
        }
        if ($request->deployed_at) {
            $query->where('deployed_at', $request->deployed_at);
        }
        if ($request->last_data_at) {
            $query->where('last_data_at', $request->last_data_at);
        }
        if ($request->created_at) {
            $query->where('created_at', $request->created_at);
        }
        if ($request->last_mail_sended_at) {
            $query->where('last_mail_sended_at', $request->last_mail_sended_at);
        }
        
        $query->join('mix','mix.id','=','measurement.mix');
        $query->join('sensors','sensors.id','=','measurement.sensor');
        $query->select('measurement.*', 'mix.title as mixTitle', 'sensors.title as sensorsTitle', 'sensors.DevEUI');
        $query->orderBy('id', 'DESC');
        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        if (Pariette::authRole('measurement', 'create', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'description' => 'required',
        //     'mix' => 'required',
        //     'sensor' => 'required',
        //     'max_temp' => 'required',
        //     'min_temp' => 'required',
        //     'last_temp' => 'required',
        //     'readed_max' => 'required',
        //     'readed_min' => 'required',
        //     'started_at' => 'required',
        //     'ended_at' => 'required',
        //     'deployed_at' => 'required',
        //     'last_data_at' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

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
            'started_at' => date("y-m-d H:i:s", strtotime($request->started_at)),
            'ended_at' => date("y-m-d H:i:s", strtotime($request->ended_at)),
            'deployed_at' => date("y-m-d H:i:s", strtotime($request->deployed_at)),
            'last_data_at' => date("y-m-d H:i:s", strtotime($request->last_data_at)),
            'created_at' => Pariette::now()
        ];

        $work = DB::table('measurement')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $data = DB::table('measurement')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        if (Pariette::authRole('measurement', 'update', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

		$validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'max_temp' => 'required',
            'min_temp' => 'required',
            'last_temp' => 'required',
            'readed_max' => 'required',
            'readed_min' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
            'deployed_at' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'max_temp' => $request->max_temp,
            'min_temp' => $request->min_temp,
            'last_temp' => $request->last_temp,
            'readed_max' => $request->readed_max,
            'readed_min' => $request->readed_min,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'deployed_at' => $request->deployed_at,
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
        if (Pariette::authRole('measurement', 'delete', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
