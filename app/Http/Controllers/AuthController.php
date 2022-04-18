<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Mail;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'forgotPassword', 'updatePassword']]);
    }

    public function login()
    {
        $response = [];
        $credentials = request(['email', 'password']);
        $token = auth()->attempt($credentials);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'E-Posta veya Şifre Hatalı'], 401);
        }
        $response['access_token'] = $token;

        $user = auth()->user();

        if ($user->status == 0) {
            return response()->json(['error' => 'Hesabınız engellenmiştir.'], 403);
        }

        
        $auth = DB::table('authority')
            ->where('user', $user->id)
            ->join('companies', 'companies.id', 'authority.company')
            ->join('projects', 'projects.id', 'authority.project')
            ->select('authority.*', 'companies.title as companyTitle', 'companies.token as companyToken', 'companies.id as companyId', 'projects.title as projectTitle', 'projects.id as projectId')
            ->get();
    
        if (count($auth) <= 0) {
          return response()->json(['error' => 'Bu Alanı Görüntüleme Yetkiniz Bulunmamaktadır'], 401);
        }

        $response['authority'] = $auth;
    
        $response['expires_in'] = auth()->factory()->getTTL() * 60;
        $response['user'] = $user;
        $response['token_type'] = 'bearer';
        Pariette::logger('user:login', $user->id);
    
        return $response;
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function forgotPassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email'=>'required|email'
		]);
		if ($validator->fails()) {
			return Hermes::send($validator->messages(), 403);
		}
		$email = $request->email;
		$code = rand(1000,9999); // 4 Karakterli random code (int)
	
		$checkEmail = DB::table('users')->where('email', $email)->first();
		if(!$checkEmail){
			return Hermes::send('ERR1000', 404);
		}
		if ($checkEmail->status == 8) {
			return Hermes::send('ERR1005', 403);
		}

		$loadCode = DB::table('users')->where('email', $email)->update([
			'confirmation_code' => $code,
		]);
		if(!$loadCode){
			return Hermes::send('ERR1001', 403);
		}
		$data = [
			'code' => $code,
		];
		 Mail::send('forgot-password', $data, function ($message) use($email){
			$message->to($email);
			$message->subject('Update Password');
		});
		if(count(Mail::failures()) > 0){
			return Hermes::send('ERR1002', 403);
		} else{
            Pariette::logger('user:forgotPassword', $email . ' şifre sıfırlama talebi gönderdi.');
            return Hermes::send("SUC1000", 200);
		}
	}

	public function updatePassword(Request $request)
	{
		$users = DB::table('users')->where([
			'email' => $request->email,
			'confirmation_code' => $request->code
		])->get();
		if(!$users->isEmpty()){
			foreach ($users as $user){
				$updatePassword =	DB::table('users')->where('id',$user->id)->update([
					'password' => Pariette::hash($request->password),
                    'confirmation_code' => null// işlem başarılıysa kodu sıfırlıyorum.
                ]);
            }
            Pariette::logger('user:updatePassword', $request->email . ' şifresini sıfırladı.');
            return Hermes::send("SUC1001", 200);
        } else {
            return Hermes::send("ERR1004", 403);
        }
	}

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}