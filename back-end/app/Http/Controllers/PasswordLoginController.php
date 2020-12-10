<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordLoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
        } else {
            return response()->json(['message' => 'Invalid username or password'], 401);
        }

        return $this->returnUserInfo(Auth::user());
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user           = new User();
        $user->email    = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        Auth::login($user);
        request()->session()->regenerate();

        return $this->returnUserInfo($user);
    }

    public function changePassword()
    {

    }

    public function forgotPassword()
    {

    }

    protected function returnUserInfo($user)
    {
        $companyService = app(CompanyService::class);
        $companies      = $companyService->getUserCompanies($user->id);

        return response(['status' => 'OK', 'user' => $user, 'companies' => CompanyResource::collection($companies)]);
    }
}
