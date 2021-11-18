<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser('TABLENAME', 'read')) {
            return Hermes::send('Authorization Error', 401);
        }
        $query = DB::table('blog')->where();

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('Not Found', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'TABLENAME', 'create')) {
            return Hermes::send('Authorization Error', 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'user' => Pariette::user(),
            'title' => $request->title,
            'status' => $request->status,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('TABLENAME')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('Create Error', 204);
    }

    public function show($id)
    {
        $data = DB::table('TABLENAME')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('TABLENAME', 'update')) {
            return Hermes::send('Authorization Error', 401);
        }
		$validator = Validator::make($request->all(), [
            'title' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'title' => $request->title,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('TABLENAME')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('Update Error', 204);
    }
    

    public function destroy($id)
    {
    }
}
