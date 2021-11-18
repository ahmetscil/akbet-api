<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompaniesController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->controlUser('companies', 'read')) {
            return Hermes::send('lng_0002', 401);
        }
        $query = DB::table('companies');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request)
    {
        if (!$this->controlUser($request->store, 'companies', 'create')) {
            return Hermes::send('lng_0002', 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'email_title' => 'required',
            'email' => 'required',
            'telephone_title' => 'required',
            'telephone' => 'required',
            'country' => 'required',
            'city' => 'required',
            'address' => 'required',
            'logo' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        $data = [
            'title' => $request->title,
            'email_title' => $request->email_title,
            'email' => $request->email,
            'telephone_title' => $request->telephone_title,
            'telephone' => $request->telephone,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'logo' => $request->logo,
            'status' => $request->status,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('companies')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($id)
    {
        $data = DB::table('companies')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id)
    {
        if (!$this->controlUser('companies', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'title' => 'required',
            'email_title' => 'required',
            'email' => 'required',
            'telephone_title' => 'required',
            'telephone' => 'required',
            'country' => 'required',
            'city' => 'required',
            'address' => 'required',
            'logo' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'title' => $request->title,
            'email_title' => $request->email_title,
            'email' => $request->email,
            'telephone_title' => $request->telephone_title,
            'telephone' => $request->telephone,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'logo' => $request->logo,
            'status' => $request->status,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('companies')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
