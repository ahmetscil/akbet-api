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
        $auth = Pariette::authRole('companies', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $query = DB::table('companies');
        if (Pariette::who('admin') == 0) {
            $query->where('companies.id', $auth->company);
        }

        if ($request->title) {
            $query->where('companies.title', 'like', '%'.$request->title.'%');
        }
        if ($request->email) {
            $query->where('companies.email', $request->email);
        }
        if ($request->telephone) {
            $query->where('companies.telephone', $request->telephone);
        }
        if ($request->country) {
            $query->where('companies.country', $request->country);
        }
        if ($request->city) {
            $query->where('companies.city', $request->city);
        }
        if ($request->address) {
            $query->where('companies.address', 'like', '%'.$request->address.'%');
        }
        if (isset($request->status)) {
            $query->where('companies.status', $request->status);
        } else {
            $query->where('companies.status', 1);
        }

        $query->join('lookup_item', 'lookup_item.id', 'companies.country');
        $query->select('companies.*', 'lookup_item.key as countryName');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('companies', 'create', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

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

        $company = DB::table('companies')->insertGetId($data);
        if ($company) {
            $prj = [
                'company' => $company,
                'code' => Pariette::random(3),
                'title' => $request->title,
                'description' => $request->title . ' Default Proje',
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
    
            $project = DB::table('projects')->insertGetId($prj);
            if ($project) {
                $auth = [
                    'user' => Pariette::user(),
                    'company' => $company,
                    'project' => $project,
                    'auth' => 1111,
                    'log' => 1111,
                    'galleries' => 1111,
                    'downlink' => 1111,
                    'companies' => 1111,
                    'lookup_item' => 1111,
                    'lookup' => 1111,
                    'sensors' => 1111,
                    'projects' => 1111,
                    'mix' => 1111,
                    'mix_calibration' => 1111,
                    'measurement' => 1111,
                    'uplink' => 1111,
                    'users' => 1111,
                    'boss' => 1,
                    'admin' => 0,
                    'status' => 1,
                    'created_at' => Pariette::now()
                ];
        
                DB::table('authority')->insert($auth);
                Pariette::logger('createCompany',$request->title . ' Firması oluşturuldu', $company, $project);

                $response = [];
                $response['company'] = $company;
                $response['project'] = $project;
                $response['auth'] = $auth;

                $newauth = DB::table('authority')
                    ->where('user', Pariette::user())
                    ->join('companies', 'companies.id', 'authority.company')
                    ->join('projects', 'projects.id', 'authority.project')
                    ->select('authority.*', 'companies.title as companyTitle', 'companies.token as companyToken', 'companies.id as companyId', 'projects.title as projectTitle', 'projects.id as projectId')
                    ->get();
                    
                $response['authority'] = $newauth;

                return Hermes::send($response, 201);
            }
    
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $auth = Pariette::authRole('companies', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $data = DB::table('companies')
        ->join('lookup_item', 'lookup_item.id', 'companies.country')
        ->select('companies.*', 'lookup_item.key as countryName')
        ->where('companies.id', $id)
        ->first();
        return Hermes::send($data, 200);
    }

    public function update(Request $request, $storeToken, $id)
    {
        $auth = Pariette::authRole('companies', 'update', $storeToken);
        if ($auth == false) {
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
        $auth = Pariette::authRole('companies', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
