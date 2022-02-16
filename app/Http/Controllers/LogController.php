<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        if (Pariette::authRole('log', 'read', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 250;

        $query = DB::table('log');
        if ($request->operation) {
            $query->where('operation', 'like', '%'.$request->operation.'%');
        }
        if ($request->info) {
            $query->where('info', 'like', '%'.$request->info.'%');
        }

        $query->join('users','users.id','=','log.user');
        $query->join('companies','companies.id','=','log.company');
        $query->join('projects','projects.id','=','log.project');
        $query->select('log.*', 'users.name as userName', 'companies.title as companyName', 'projects.title as projectName');

        $data = $query->offset($offset)->limit($limit)->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        if (Pariette::authRole('log', 'create', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        $validator = Validator::make($request->all(), [
            'operation' => 'required',
            'info' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'user' => Pariette::user(),
            'company' => $request->company,
            'project' => $request->project,
            'operation' => $request->operation,
            'info' => $request->info,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('log')->insert($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        if (Pariette::authRole('log', 'read', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        $data = DB::table('log')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        if (Pariette::authRole('log', 'update', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

		$validator = Validator::make($request->all(), [
            'operation' => 'required',
            'info' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'user' => Pariette::user(),
            'company' => $request->company,
            'project' => $request->project,
            'operation' => $request->operation,
            'info' => $request->info,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('log')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($storeToken, $id)
    {
        if (Pariette::authRole('log', 'delete', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
