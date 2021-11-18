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
    public function index(Request $request)
    {
        if (!$this->controlUser('mix', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('mix');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'mix', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'project' => 'required',
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

        $data = [
            'user' => $request->user,
            'project' => $request->project,
            'title' => $request->title,
            'description' => $request->description,
            'activation_energy' => $request->activation_energy,
            'temperature' => $request->temperature,
            'a' => $request->a,
            'b' => $request->b,
            'status' => $request->status,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('mix')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('mix')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('mix', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'user' => 'required',
            'project' => 'required',
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
    
        $data = [
            'user' => $request->user,
            'project' => $request->project,
            'title' => $request->title,
            'description' => $request->description,
            'activation_energy' => $request->activation_energy,
            'temperature' => $request->temperature,
            'a' => $request->a,
            'b' => $request->b,
            'status' => $request->status,
            'updated_at' => Pariette::now()
        ];

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
