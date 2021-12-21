<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SensorsController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser($request->store, 'sensors', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('sensors');

        if ($request->project) {
            $query->where('project', $request->project);
        }
        if ($request->DevEUI) {
            $query->where('DevEUI', $request->DevEUI);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->title) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }
        if ($request->description) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->sensor_no) {
            $query->where('sensor_no', $request->sensor_no);
        }
        if ($request->created_at) {
            $query->where('created_at', $request->created_at);
        }

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'sensors', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        // $validator = Validator::make($request->all(), [
        //     'project' => 'required',
        //     'DevEUI' => 'required',
        //     'type' => 'required',
        //     'title' => 'required',
        //     'description' => 'required',
        //     'status' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

        $data = [
            'project' => $request->project,
            'DevEUI' => $request->DevEUI,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ? $request->status : 1,
            'sensor_no' => $request->sensor_no,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('sensors')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('sensors')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('sensors', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'project' => 'required',
            'DevEUI' => 'required',
            'type' => 'required',
            'title' => 'required',
            'description' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'project' => $request->project,
            'DevEUI' => $request->DevEUI,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'sensor_no' => $request->sensor_no,
            'status' => $request->status,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('sensors')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
