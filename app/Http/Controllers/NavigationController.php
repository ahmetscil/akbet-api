<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NavigationController extends Controller
{
    public function index(Request $request, $storeToken)
    {
		$user = Auth::id();
		$company = DB::table('companies')->where('token', Pariette::token($storeToken))->first();
		$query = DB::table('authority')->where(['user' => $user, 'company' => $company->id]);
        $auth = $query->first();

		// if (($auth->boss === 1) || ($auth->admin === 1)) {
		// 	return true;
		// }
		$auth_auth = str_split($auth->auth);
		$auth_log = str_split($auth->log);
		$auth_galleries = str_split($auth->galleries);
		$auth_downlink = str_split($auth->downlink);
		$auth_companies = str_split($auth->companies);
		$auth_lookup_item = str_split($auth->lookup_item);
		$auth_lookup = str_split($auth->lookup);
		$auth_sensors = str_split($auth->sensors);
		$auth_projects = str_split($auth->projects);
		$auth_mix = str_split($auth->mix);
		$auth_mix_calibration = str_split($auth->mix_calibration);
		$auth_measurement = str_split($auth->measurement);
		$auth_uplink = str_split($auth->uplink);
		$auth_users = str_split($auth->users);
		$auth_boss = $auth->boss;
		$auth_admin = $auth->admin;

        $navSelect = array();

        if(($auth_auth[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'auth');
        }
        if(($auth_log[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'log');
        }
        if(($auth_galleries[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'galleries');
        }
        if(($auth_downlink[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'downlink');
        }
        if(($auth_companies[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'companies');
        }
        if(($auth_lookup_item[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'lookup_item');
        }
        if(($auth_lookup[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'lookup');
        }
        if(($auth_sensors[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'sensors');
        }
        if(($auth_projects[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'projects');
        }
        if(($auth_mix[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'mix');
        }
        if(($auth_mix_calibration[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'mix_calibration');
        }
        if(($auth_measurement[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'measurement');
        }
        if(($auth_uplink[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'uplink');
        }
        if(($auth_users[1] == 1) || ($auth_boss == 1) || ($auth_admin == 1)) {
            array_push($navSelect, 'users');
        }

        $navigation = array();
        foreach ($navSelect as $n) {
            $query = DB::table('navigation');

            // $query->where('company', $request->company ? $request->company : Pariette::company(Pariette::token($storeToken), 'id'));
            $query->where('type', $request->type ? $request->type : 'web');
            $query->where('status', $request->status ? $request->status : 1);
            $query->where('code', $n);
            $data = $query->first();
            if ($data) {
                array_push($navigation, $data);
            }
        }
        return Hermes::send($navigation, 200);

        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('navigation', 'create', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
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

        $work = DB::table('navigation')->insertGetId($data);
        if ($work) {
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $auth = Pariette::authRole('navigation', 'show', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $data = DB::table('navigation')->find($id);
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $id, $storeToken)
    {
        $auth = Pariette::authRole('navigation', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
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

        $update = DB::table('navigation')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($storeToken, $id)
    {
        $auth = Pariette::authRole('navigation', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }
    }
}
