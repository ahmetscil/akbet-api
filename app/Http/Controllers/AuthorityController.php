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
    public function index(Request $request)
    {
        if (!$this->controlUser('authority', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('authority');

        if ($request->user) {
            $query->where('user', $request->user);
        }
        if ($request->company) {
            $query->where('company', $request->company);
        }
        if ($request->project) {
            $query->where('project', $request->project);
        }

        $query->join('users','users.id','=','authority.user');
        $query->join('companies','companies.id','=','authority.company');
        $query->join('projects','projects.id','=','authority.project');
        $query->select('authority.*', 'users.name as userName', 'companies.title as companyName', 'projects.title as projectName');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'authority', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'company' => 'required',
            'project' => 'required',
            'crud' => 'required',
            'boss' => 'required',
            'admin' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'user' => $request->user,
            'company' => $request->company,
            'project' => $request->project,
            'crud' => $request->crud,
            'boss' => $request->boss,
            'admin' => $request->admin,
            'status' => $request->status,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('authority')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('authority')->find($id);
        return Hermes::send($data, 200);
    }

    public function update(Request $request, $id)
    {
        if (!$this->controlUser('authority', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'user' => 'required',
            'company' => 'required',
            'project' => 'required',
            'crud' => 'required',
            'boss' => 'required',
            'admin' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'user' => $request->user,
            'company' => $request->company,
            'project' => $request->project,
            'crud' => $request->crud,
            'boss' => $request->boss,
            'admin' => $request->admin,
            'status' => $request->status,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('authority')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }    

    public function destroy($id)
    {
    }
}
