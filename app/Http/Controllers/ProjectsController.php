<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        if (!$this->controlUser($request->store, 'projects', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('projects');

        $query->where('company', Pariette::company($storeToken, 'id'));
        if ($request->code) {
            $query->where('code', $request->code);
        }
        if ($request->title) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }
        if ($request->description) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }
        if ($request->email) {
            $query->where('email', $request->email);
        }
        if ($request->telephone) {
            $query->where('telephone', $request->telephone);
        }
        if ($request->country) {
            $query->where('country', $request->country);
        }
        if ($request->city) {
            $query->where('city', $request->city);
        }
        if ($request->address) {
            $query->where('address', $request->address);
        }
        if ($request->started_at) {
            $query->where('started_at', $request->started_at);
        }
        if ($request->ended_at) {
            $query->where('ended_at', $request->ended_at);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $query->join('companies','companies.id','=','projects.company');
        $query->select('projects.*', 'companies.title as companName');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200, Pariette::company($storeToken));
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'projects', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        // $validator = Validator::make($request->all(), [
        //     'company' => 'required',
        //     'code' => 'required',
        //     'title' => 'required',
        //     'description' => 'required',
        //     'email_title' => 'required',
        //     'email' => 'required',
        //     'telephone_title' => 'required',
        //     'telephone' => 'required',
        //     'country' => 'required',
        //     'city' => 'required',
        //     'address' => 'required',
        //     'logo' => 'required',
        //     'started_at' => 'required',
        //     'ended_at' => 'required',
        //     'status' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

        $data = [
            'company' => $request->company,
            'code' => $request->code,
            'title' => $request->title,
            'description' => $request->description,
            'email_title' => $request->email_title,
            'email' => $request->email,
            'telephone_title' => $request->telephone_title,
            'telephone' => $request->telephone,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'logo' => $request->logo,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'status' => $request->status ? $request->status : 1,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('projects')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $data = DB::table('projects')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('projects', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'company' => 'required',
            'code' => 'required',
            'title' => 'required',
            'description' => 'required',
            'email_title' => 'required',
            'email' => 'required',
            'telephone_title' => 'required',
            'telephone' => 'required',
            'country' => 'required',
            'city' => 'required',
            'address' => 'required',
            'logo' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'company' => $request->company,
            'code' => $request->code,
            'title' => $request->title,
            'description' => $request->description,
            'email_title' => $request->email_title,
            'email' => $request->email,
            'telephone_title' => $request->telephone_title,
            'telephone' => $request->telephone,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'logo' => $request->logo,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'status' => $request->status,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('projects')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
