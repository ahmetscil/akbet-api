<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
class Hermes {
	public static function send($data, $code, $info = null, $key = null): array
	{
		$redis = Redis::connection();
		$res = [];
		$res['code'] = $code;
		if ($info) {
			$res['info'] = $info;
		}
		if ($key) {
			$res['key'] = $key;
		}
		switch ($code) {
			case 200:
				$res['status'] = true;
				$res['data'] = $data;
				break;
			case 201:
				$res['status'] = true;
				$res['data'] = $data;
				break;			
			default:
				$res['status'] = false;
				$res['error'] = $data;
				break;
		}
		return $res;
	}

	public static function discord($c, $t, $d, $color = '7506394') {
    Http::post('discordurl', [
      'content' => $c,
      'embeds' => [
				[
					'title' => $t,
					'description' => $d,
					'color' => $color,
				]
      ]
		]);
	}

}
