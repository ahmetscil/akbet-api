<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UsersController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        if (Pariette::authRole('users', 'read', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        $query = DB::table('users');

        if ($request->name) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->email) {
            $query->where('email', $request->email);
        }
        if ($request->phone) {
            $query->where('phone', $request->phone);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (Pariette::authRole('users', 'create', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        // $validator = $request->validate([
        //     'name' => 'required',
        //     'email' => 'required',
        //     'password' => 'required',
        //     'status' => 'required'
        // ]);

        // if ($validator['errors']) {
        //     return Hermes::send($validator, 403);
        // }
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'photo' => $request->photo,
            'phone' => $request->phone,
            'ip' => Pariette::getIp(),
            'password' => Pariette::hash($request->password),
            'status' => $request->status ? $request->status : 1,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('users')->insert($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('users')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (Pariette::authRole('users', 'update', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

		// $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'email' => 'required',
        //     'photo' => 'required',
        //     'phone' => 'required',
        //     'status' => 'required'
        // ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'photo' => $request->photo,
            'phone' => $request->phone,
            'ip' => Pariette::getIp(),
            'status' => $request->status,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('users')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
        if (Pariette::authRole('users', 'delete', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
