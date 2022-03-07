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
        $auth = Pariette::authRole('measurement', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

        $query = DB::table('measurement');

        if ($request->name) {
            $query->where('measurement.name', 'like', '%'.$request->name.'%');
        }
        if ($request->description) {
            $query->where('measurement.description', 'like', '%'.$request->description.'%');
        }
        if ($request->mix) {
            $query->where('measurement.mix', $request->mix);
        }
        if ($request->sensor) {
            $sensor = DB::table('sensors')->where('id', $request->sensor)->first();
            $query->where('measurement.sensor', $sensor->id);
        }
        if ($request->max_temp) {
            $query->where('measurement.max_temp', $request->max_temp);
        }
        if ($request->min_temp) {
            $query->where('measurement.min_temp', $request->min_temp);
        }
        if ($request->last_temp) {
            $query->where('measurement.last_temp', $request->last_temp);
        }
        if ($request->readed_max) {
            $query->where('measurement.readed_max', $request->readed_max);
        }
        if ($request->readed_min) {
            $query->where('measurement.readed_min', $request->readed_min);
        }
        if ($request->started_at) {
            $query->where('measurement.started_at', $request->started_at);
        }
        if ($request->ended_at) {
            $query->where('measurement.ended_at', $request->ended_at);
        }
        if ($request->deployed_at) {
            $query->where('measurement.deployed_at', $request->deployed_at);
        }
        if ($request->last_data_at) {
            $query->where('measurement.last_data_at', $request->last_data_at);
        }
        if ($request->created_at) {
            $query->where('measurement.created_at', $request->created_at);
        }
        if ($request->last_mail_sended_at) {
            $query->where('measurement.last_mail_sended_at', $request->last_mail_sended_at);
        }
        if (isset($request->status)) {
            $query->where('measurement.status', $request->status);
        } else {
            $query->where('measurement.status', 1);
        }
        
        if ($request->project) {
            $sensors = DB::table('sensors')->where('project', $request->project)->select('sensors.id')->get();
            $a = array();
            foreach ($sensors as $s) {
                array_push($a, $s->id);
            }
            $query->whereIn('measurement.sensor', $a);
        }

        $query->join('mix','mix.id','=','measurement.mix');
        $query->join('sensors','sensors.id','=','measurement.sensor');
        $query->join('projects','projects.id','=','sensors.project');
        $query->select('measurement.*', 'mix.title as mixTitle', 'sensors.title as sensorsTitle', 'sensors.DevEUI', 'projects.title as projectName');
        $query->orderBy('id', 'DESC');
        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('measurement', 'create', $storeToken);
        if ($auth == false) {
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
            'status' => $request->status ? $request->status : 1,
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
        $auth = Pariette::authRole('measurement', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

		$validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'deployed_at' => $request->deployed_at,
            'status' => $request->status,
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
        $auth = Pariette::authRole('measurement', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
