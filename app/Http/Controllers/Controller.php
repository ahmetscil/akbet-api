<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function controlUser ($store, $where, $what) {
		return true;
		// $control = DB::table('authority')
		// 	->where('user', Auth::id())
		// 	->where('company', $store)
		// 	->select('admin', 'moderator', 'standart', $where)
		// 	->first();
		// if ($control) {
		// 	$s = str_split($control->$where);
		// 	switch ($what) {
		// 		case 'create':
		// 			$w = 1;
		// 			break;
		// 		case 'read':
		// 			$w = 2;
		// 			break;
		// 		case 'update':
		// 			$w = 3;
		// 			break;
		// 		default:
		// 			$w = 0;
		// 			if ($control->$what == 1) {
		// 				$ok = 1;
		// 			} else {
		// 				$ok = 0;
		// 			}
		// 			break;
		// 	}
		// 	if ($s[$w] == 1) {
		// 		$ok = true;
		// 	} else {
		// 		$ok = false;
		// 	}
		// 	return $ok;
		// } else {
		// 	return false;
		// }
	}
}
