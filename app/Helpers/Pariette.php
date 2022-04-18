<?php

namespace App\Helpers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
class Pariette {
	
	public static function logger($operation, $info = 'autoLog', $company = null, $project = null)
	{
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
			if (array_key_exists($key, $_SERVER) === true){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					$ip = trim($ip); // just to be safe
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
		$tIp = request()->ip();

		DB::table('log')->insert([
			'user' => Auth::id() ? Auth::id() : 0,
			'company' => $company,
			'project' => $project,
			'operation' => $operation,
			'info' => $info . ' | ip: ' . $tIp,
			'created_at' => Carbon::now()
		]);
		return true;
	}

	public static function user () {
		return Auth::id();
	}

	public static function now () {
		return Carbon::now();
	}

	public static function slug ($key) {
		return Str::slug($key, '-');
	}

	public static function clear ($key) {
		return Str::slug($key, '');
	}

	public static function who ($w) {
		return Auth::user()[$w];
	}

	public static function hash ($key) {
		return Hash::make($key);
	}

	public static function random ($key) {
		return Str::random($key);
	}

	public static function company ($key, $w = null) {
		$c = DB::table('companies')->where('token', $key)->first();
		if ($w != null) {
			return $c->$w;
		} else {
			return $c;
		}
	}

	// public static function redis ($key) {
	// 	$redis = Redis::connection();
	// 	return json_decode($redis->get($key), true);
	// }

	// public static function redisSet ($key, $val) {
	// 	$redis = Redis::connection();
	// 	$redis->set($key, json_encode($val));
	// 	return true;
	// }

	// public static function redisDel ($key) {
	// 	$redis = Redis::connection();
	// 	$redis->del($key);
	// 	return true;
	// }

	public static function getIp() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
			if (array_key_exists($key, $_SERVER) === true){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					$ip = trim($ip); // just to be safe
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
		return request()->ip();
	}

	public static function setVersion($company) {
		$v = DB::table('version')->where('store', $company)->first();
		$s = str_split($v->version);
		$n = intval($s[4]) + 1;
		$new = $s[0].$s[1].$s[2].$s[3].$n.$s[5].$s[6];
		DB::table('version')->where('store', $company)->update([
			'version' => $new,
			'updated_at' => Carbon::now() 
		]);
		return true;
	}
	public static function token ($token) {
		$t = explode('-', $token);
		return $t[0];
	}

	public static function authRole ($where, $what, $store, $project = null) {
		$user = Auth::id();

		$userStatus = Auth::user()['status'];
		if ($userStatus == 0) {
			auth()->logout();
			return false;
		}

		$exp = explode('-', $store);
		$company = DB::table('companies')->where('token', $exp[0])->first();
		$query = DB::table('authority')->where(['user' => $user, 'company' => $company->id, 'project' => $exp[1]]);
		$auth = $query->first();
		if (!$auth) {
			return false;
		}
		if (($auth->boss === 1) || ($auth->admin === 1)) {
			// false dönüyor çünkü uyarı vermek istemiyoruz :)
			return $auth;
		}

		$crud = str_split($auth->$where);

		switch ($what) {
			case 'create':
				$w = 0;
				break;
			case 'read':
				$w = 1;
				break;
			case 'update':
				$w = 2;
				break;
			case 'delete':
				$w = 3;
				break;
		}

		$control = $crud[$w];
		if ($control == 1) {
			$data = $auth;
		} else {
			$data = false;
		}
		// yetkisi yoksa true dönecek!
		return $data;
	}
}
