<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MixController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        if ($request->store) {
            $store = $request->store;
        } else {
            $store = $storeToken;
        }
        if (!$this->controlUser($store, 'mix', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('mix');

        if ($request->user) {
            $query->where('user', $request->user);
        }
        if ($request->project) {
            $query->where('project', 'like', '%'.$request->project.'%');
        }
        if ($request->title) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }
        if ($request->description) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }
        if ($request->activation_energy) {
            $query->where('activation_energy', $request->activation_energy);
        }
        if ($request->temperature) {
            $query->where('temperature', $request->temperature);
        }
        if ($request->a) {
            $query->where('a', $request->a);
        }
        if ($request->b) {
            $query->where('b', $request->b);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $query->join('users','users.id','=','mix.user');
        $query->join('projects','projects.id','=','mix.project');
        $query->select('mix.*', 'users.name as userName', 'projects.title as projectName');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if ($request->store) {
            $store = $request->store;
        } else {
            $store = $storeToken;
        }
        if (!$this->controlUser($store, 'mix', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        // $validator = Validator::make($request->all(), [
        //     'user' => 'required',
        //     'project' => 'required',
        //     'title' => 'required',
        //     'description' => 'required',
        //     'activation_energy' => 'required',
        //     'temperature' => 'required',
        //     'a' => 'required',
        //     'b' => 'required',
        //     'status' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

        $data = [
            'user' => Pariette::user(),
            'project' => $request->project,
            'title' => $request->title,
            'description' => $request->description,
            'activation_energy' => $request->activation_energy,
            'temperature' => $request->temperature,
            'a' => $request->a,
            'b' => $request->b,
            'status' => $request->status ? $request->status : 1,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('mix')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $data = DB::table('mix')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        if ($request->store) {
            $store = $request->store;
        } else {
            $store = $storeToken;
        }
        if (!$this->controlUser($store, 'mix', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'activation_energy' => 'required',
            'temperature' => 'required',
            'a' => 'required',
            'b' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [];
        if ($request->title) {
            $data['title'] = $request->title;
        }
        if ($request->description) {
            $data['description'] = $request->description;
        }
        if ($request->activation_energy) {
            $data['activation_energy'] = $request->activation_energy;
        }
        if ($request->temperature) {
            $data['temperature'] = $request->temperature;
        }
        if ($request->a) {
            $data['a'] = $request->a;
        }
        if ($request->b) {
            $data['b'] = $request->b;
        }
        if ($request->status) {
            $data['status'] = $request->status;
        }
        $data['updated_at'] = Pariette::now();

        $update = DB::table('mix')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
