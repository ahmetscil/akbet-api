<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthorityController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('auth', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $query = DB::table('authority');

        if ($request->user) {
            $query->where('authority.user', $request->user);
        }
        if ($request->company) {
            $query->where('authority.company', $request->company);
        }
        if ($request->project) {
            $query->where('authority.project', $request->project);
        }

        $query->join('users','users.id','=','authority.user');
        $query->join('companies','companies.id','=','authority.company');
        $query->join('projects','projects.id','=','authority.project');
        $query->select('authority.*', 'users.name as userName', 'companies.title as companyName', 'projects.title as projectName');

        if (isset($request->status)) {
            $query->where('authority.status', $request->status);
        } else {
            $query->where('authority.status', 1);
        }

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('auth', 'create', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }


        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'company' => 'required',
            'project' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'user' => $request->user,
            'company' => $request->company,
            'project' => $request->project,
            'auth' => '0100',
            'log' => '0100',
            'galleries' => '0100',
            'downlink' => '0100',
            'companies' => '0100',
            'lookup_item' => '0100',
            'lookup' => '0100',
            'sensors' => '0100',
            'projects' => '0100',
            'mix' => '0100',
            'mix_calibration' => '0100',
            'measurement' => '0100',
            'uplink' => '0100',
            'users' => '0100',
            'boss' => 0,
            'admin' => 0,
            'status' => $request->status,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('authority')->insertGetId($data);
        if ($work) {
            Pariette::logger('user:authority', $request->user . ' yetki tanımlandı.', $request->company, $request->project);
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $auth = Pariette::authRole('auth', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }


        $query = DB::table('authority');
        $query->where('authority.id', $id);
        $query->join('users','users.id','=','authority.user');
        $query->join('companies','companies.id','=','authority.company');
        $query->join('projects','projects.id','=','authority.project');
        $query->select('authority.*', 'users.name as userName', 'companies.title as companyName', 'projects.title as projectName');
        
        $data = $query->first();

        return Hermes::send($data, 200);
    }

    public function update(Request $request, $storeToken, $id)
    {
        $auth = Pariette::authRole('auth', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }


        $validator = Validator::make($request->all(), [
            'auth' => 'required',
            'log' => 'required',
            'galleries' => 'required',
            'downlink' => 'required',
            'companies' => 'required',
            'lookup_item' => 'required',
            'lookup' => 'required',
            'sensors' => 'required',
            'projects' => 'required',
            'mix' => 'required',
            'mix_calibration' => 'required',
            'measurement' => 'required',
            'uplink' => 'required',
            'users' => 'required',
            'boss' => 'required',
            'admin' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [];
        if (isset($request->auth)) {
            $data['auth'] = $request->auth;
        }
        if (isset($request->log)) {
            $data['log'] = $request->log;
        }
        if (isset($request->galleries)) {
            $data['galleries'] = $request->galleries;
        }
        if (isset($request->downlink)) {
            $data['downlink'] = $request->downlink;
        }
        if (isset($request->companies)) {
            $data['companies'] = $request->companies;
        }
        if (isset($request->lookup_item)) {
            $data['lookup_item'] = $request->lookup_item;
        }
        if (isset($request->lookup)) {
            $data['lookup'] = $request->lookup;
        }
        if (isset($request->sensors)) {
            $data['sensors'] = $request->sensors;
        }
        if (isset($request->projects)) {
            $data['projects'] = $request->projects;
        }
        if (isset($request->mix)) {
            $data['mix'] = $request->mix;
        }
        if (isset($request->mix_calibration)) {
            $data['mix_calibration'] = $request->mix_calibration;
        }
        if (isset($request->measurement)) {
            $data['measurement'] = $request->measurement;
        }
        if (isset($request->uplink)) {
            $data['uplink'] = $request->uplink;
        }
        if (isset($request->users)) {
            $data['users'] = $request->users;
        }

        if (isset($request->boss)) {
            $data['boss'] = $request->boss;
        }
        if (isset($request->admin)) {
            $data['admin'] = $request->admin;
        }
        if (isset($request->status)) {
            $data['status'] = $request->status;
        }

        $data['updated_at'] = Pariette::now();

        $update = DB::table('authority')->where('id', $id)->update($data);
        
        if ($update) {
            Pariette::logger('user:authority', 'auth.id:' .$id . ' yetkiler düzenlendi.', $storeToken, null);
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }    

    public function destroy($storeToken, $id)
    {
        $auth = Pariette::authRole('auth', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $update = DB::table('authority')->where('id', $id)->update(['status' => 0, 'updated_at' => Pariette::now()]);
        if ($update) {
            return Hermes::send('lng_0006', 200);
        }
        return Hermes::send('lng_0004', 204);

    }
}
