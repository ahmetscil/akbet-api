<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GalleriesController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('galleries', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $query = DB::table('galleries');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('galleries', 'create', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'photo' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'project' => $request->project,
            'title' => $request->title,
            'photo' => $request->photo,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('galleries')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $auth = Pariette::authRole('galleries', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $data = DB::table('galleries')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        $auth = Pariette::authRole('galleries', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }


        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'photo' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'project' => $request->project,
            'title' => $request->title,
            'photo' => $request->photo,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('galleries')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($storeToken, $id)
    {
        $auth = Pariette::authRole('galleries', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

    }
}
