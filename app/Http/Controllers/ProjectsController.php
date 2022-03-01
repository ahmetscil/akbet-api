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
        $auth = Pariette::authRole('projects', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

        $query = DB::table('projects');

        if (($auth->admin == 0) && ($auth->boss == 0)) {
            $query->where('projects.company', $auth->company);
            $query->where('projects.id', $auth->project);
        } else if (($auth->boss == 1) && ($auth->admin == 0)) {
            $query->where('projects.company', $auth->company);
        }


        if ($request->code) {
            $query->where('projects.code', $request->code);
        }
        if ($request->title) {
            $query->where('projects.title', 'like', '%'.$request->title.'%');
        }
        if ($request->description) {
            $query->where('projects.description', 'like', '%'.$request->description.'%');
        }
        if ($request->email) {
            $query->where('projects.email', $request->email);
        }
        if ($request->telephone) {
            $query->where('projects.telephone', $request->telephone);
        }
        if ($request->country) {
            $query->where('projects.country', $request->country);
        }
        if ($request->city) {
            $query->where('projects.city', $request->city);
        }
        if ($request->address) {
            $query->where('projects.address', $request->address);
        }
        if ($request->started_at) {
            $query->where('projects.started_at', $request->started_at);
        }
        if ($request->ended_at) {
            $query->where('projects.ended_at', $request->ended_at);
        }
        if (isset($request->status)) {
            $query->where('projects.status', $request->status);
        } else {
            $query->where('projects.status', 1);
        }

        $query->join('companies','companies.id','=','projects.company');
        $query->select('projects.*', 'companies.title as companName');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200, Pariette::company(Pariette::token($storeToken)));
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('projects', 'create', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
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
        $auth = Pariette::authRole('projects', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $data = DB::table('projects')
            ->where('projects.id', $id)
            ->join('companies', 'companies.id', 'projects.company')
            ->select('projects.*', 'companies.title as companyName')
            ->first();
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        $auth = Pariette::authRole('projects', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'title' => 'required',
            'country' => 'required',
            'city' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [];
        if ($request->code) {
            $data['code'] = $request->code;
        }
        
        if ($request->title) {
            $data['title'] = $request->title;
        }
        
        if ($request->description) {
            $data['description'] = $request->description;
        }
        
        if ($request->email_title) {
            $data['email_title'] = $request->email_title;
        }
        
        if ($request->email) {
            $data['email'] = $request->email;
        }
        
        if ($request->telephone_title) {
            $data['telephone_title'] = $request->telephone_title;
        }
        
        if ($request->telephone) {
            $data['telephone'] = $request->telephone;
        }
        
        if ($request->country) {
            $data['country'] = $request->country;
        }
        
        if ($request->city) {
            $data['city'] = $request->city;
        }
        
        if ($request->address) {
            $data['address'] = $request->address;
        }
        
        if ($request->logo) {
            $data['logo'] = $request->logo;
        }
        
        if ($request->started_at) {
            $data['started_at'] = $request->started_at;
        }
        
        if ($request->ended_at) {
            $data['ended_at'] = $request->ended_at;
        }
        
        if (isset($request->status)) {
            $data['status'] = $request->status;
        }
        
        $data['updated_at'] = Pariette::now();

        $update = DB::table('projects')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($storeToken, $id)
    {
        $auth = Pariette::authRole('projects', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
