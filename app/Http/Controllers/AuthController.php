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
    
        $auth = DB::table('authority')
            ->where('user', $user->id)
            ->join('companies', 'companies.id', 'authority.company')
            ->join('projects', 'projects.id', 'authority.project')
            ->select('authority.*', 'companies.title as companyTitle', 'companies.token as companyToken', 'companies.id as companyId', 'projects.title as projectTitle', 'projects.id as projectId')
            ->get();
    
        if (count($auth) <= 0) {
          return response()->json(['error' => 'Unauthorized'], 401);
        }

        // $companies = array();
        // $projects = array();
        // foreach($auth as $a) {
        //     $company = DB::table('companies')->where('id', $a->company)->first();
        //     $company->projects = DB::table('projects')->where('id', $a->project)->first();
        //     $a->companyData = $company;
        // }
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