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
        if (Pariette::authRole('projects', 'read', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

        $query = DB::table('projects');

        if ($request->company) {
            $query->where('company', $request->company);
        } else {
            $query->where('company', Pariette::company(Pariette::token($storeToken), 'id'));
        }
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
            return Hermes::send($data, 200, Pariette::company(Pariette::token($storeToken)));
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (Pariette::authRole('projects', 'create', $storeToken)) {
            return Hermes::send('lng_0002', 403);
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
        if (Pariette::authRole('projects', 'read', $storeToken)) {
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
        if (Pariette::authRole('projects', 'update', $storeToken)) {
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
            'description' => 'required',
            'email_title' => 'required',
            'email' => 'required',
            'telephone_title' => 'required',
            'telephone' => 'required',
            'country' => 'required',
            'city' => 'required',
            'address' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
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
        
        if ($request->status) {
            $data['status'] = $request->status;
        }
        
        $data['updated_at'] = Pariette::now();

        $update = DB::table('projects')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
        if (Pariette::authRole('projects', 'delete', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
