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
        $auth = Pariette::authRole('users', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

		$exp = explode('-', $storeToken);
		$company = DB::table('companies')->where('token', $exp[0])->first();
		$query = DB::table('authority')->where(['company' => $company->id,'project' => $exp[1]])->join('users', 'users.id', 'authority.user')->select('users.name as userName', 'users.phone', 'users.email', 'authority.*');
        
        if (isset($request->status)) {
            $query->where('users.status', $request->status);
        } else {
            $query->where('users.status', 1);
        }

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('users', 'create', $storeToken);
        if ($auth == false) {
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
            'admin' => 0,
            'ip' => Pariette::getIp(),
            'password' => Pariette::hash($request->password),
            'status' => $request->status ? $request->status : 1,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('users')->insertGetId($data);
        if ($work) {

            $exp = explode('-', $storeToken);
            $company = DB::table('companies')->where('token', $exp[0])->first();

            $userAuth = [
                'user' => $work,
                'company' => $company->id,
                'project' => $exp[1],
                'auth' => '0110',
                'log' => '0000',
                'galleries' => '0110',
                'downlink' => '0110',
                'companies' => '0000',
                'lookup_item' => '0110',
                'lookup' => '0110',
                'sensors' => '0110',
                'projects' => '0110',
                'mix' => '0110',
                'mix_calibration' => '0110',
                'measurement' => '0110',
                'uplink' => '0110',
                'users' => '0010',
                'boss' => 0,
                'admin' => 0,
                'status' => 1,
                'created_at' => Pariette::now()
            ];
    
            DB::table('authority')->insert($userAuth);
    

            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $auth = Pariette::authRole('users', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $authData = DB::table('authority')->find($id);
        $user = $authData->user;

        $data = DB::table('users')->find($user);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        $auth = Pariette::authRole('users', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

		// $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'email' => 'required',
        //     'photo' => 'required',
        //     'phone' => 'required',
        //     'status' => 'required'
        // ]);
		// if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
		// }
    
        $data = [];
        if (isset($request->name)) {
            $data['name'] = $request->name;
        }
        if (isset($request->email)) {
            $data['email'] = $request->email;
        }
        if (isset($request->photo)) {
            $data['photo'] = $request->photo;
        }
        if (isset($request->phone)) {
            $data['phone'] = $request->phone;
        }
        if (isset($request->status)) {
            $data['status'] = $request->status;
        }
        $data['ip'] = Pariette::getIp();
        $data['updated_at'] = Pariette::now();


        $authData = DB::table('authority')->find($id);
        $user = $authData->user;

        $update = DB::table('users')->where('id', $user)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($storeToken, $id)
    {
        $auth = Pariette::authRole('users', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
