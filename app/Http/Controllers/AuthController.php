<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ],[
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah pernah terdaftar',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password tidak boleh kurang dari 6 karakter',
            'password.max' => 'Password tidak boleh kurang dari 50 karakter',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        try {
            $register = new User();
            $register->name = $request->name;
            $register->email = $request->email;
            $register->password = bcrypt($request->password);
            $register->save();

            $data = $register;

            return $this->successResponse('Berhasil Register', $data);
        } catch (\Throwable $th) {
            return $this->failedResponse('Gagal Register');
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ],[
            'email.required' => 'Email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password tidak boleh kurang dari 6 karakter',
            'password.max' => 'Password tidak boleh kurang dari 50 karakter',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        try {
            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['message' => 'Login Gagal'], 400);
            }

            $data = $this->respondWithToken($token);

            return response()->json(['message' => 'Login Berhasil', 'data' => $data], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        try {
            $data = DB::table('users')->where('id', Auth::user()->id)->first();
            return response()->json(['message' => 'Pengambilan data berhasil', 'data' => ['profile' => $data ]], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
    public function refreshToken()
    {
        try {
            $data = $this->respondWithToken(Auth::refresh());
            return response()->json(['message' => 'Refresh token berhasil', 'data' => $data], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            auth()->logout();

            return response()->json(['message' => 'Berhasil Logout'], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
