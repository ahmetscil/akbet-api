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
    public function index(Request $request, $storeToken)
    {
        if (Pariette::authRole('companies', 'read', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        $query = DB::table('companies');

        if ($request->title) {
            $query->where('title', 'like', '%'.$request->title.'%');
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
            $query->where('address', 'like', '%'.$request->address.'%');
        }
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->whereNotIn('status', [9, 0]);
        }

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        if (Pariette::authRole('companies', 'create', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }

        // $validator = Validator::make($request->all(), [
        //     'title' => 'required',
        //     'email_title' => 'required',
        //     'email' => 'required',
        //     'telephone_title' => 'required',
        //     'telephone' => 'required',
        //     'country' => 'required',
        //     'city' => 'required',
        //     'address' => 'required',
        //     'logo' => 'required',
        //     'status' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

        $data = [
            'token' => Pariette::random(8),
            'title' => $request->title,
            'email_title' => $request->email_title,
            'email' => $request->email,
            'telephone_title' => $request->telephone_title,
            'telephone' => $request->telephone,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'logo' => $request->logo,
            'status' => $request->status ? $request->status : 1,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('companies')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        if (Pariette::authRole('companies', 'read', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }
        $data = DB::table('companies')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        if (Pariette::authRole('companies', 'update', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }
		$validator = Validator::make($request->all(), [
            'title' => 'required',
            'email' => 'required',
            'telephone' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [];
        $data['title'] = $request->title;

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
    
        $data['status'] = $request->status;
        $data['updated_at'] = Pariette::now();

        $update = DB::table('companies')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
        if (Pariette::authRole('companies', 'delete', $storeToken)) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
