<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login()
    {
        $response = [];
        $credentials = request(['email', 'password']);
        $token = auth()->attempt($credentials);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $response['access_token'] = $token;

        // return $this->respondWithToken($token);

        $user = auth()->user();
    
        $auth = DB::table('authority')->where('user', $user->id)->get();
    
        if (count($auth) <= 0) {
          return response()->json(['error' => 'Unauthorized'], 401);
        }

        $companies = array();
        $projects = array();
        foreach($auth as $a) {
            $c = DB::table('companies')->where('id', $a->company)->first();
            array_push($companies, $c);
            $p = DB::table('projects')->where('id', $a->project)->first();
            array_push($projects, $p);
        }
        $response['company'] = $companies;
        $response['project'] = $projects;
    
        $response['authority'] = $auth;
    
        $response['expires_in'] = auth()->factory()->getTTL() * 60;
        $response['user'] = $user;
        $response['token_type'] = 'bearer';
    
        return $response;

    }

    public function me()
    {
        return response()->json(auth()->user());
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