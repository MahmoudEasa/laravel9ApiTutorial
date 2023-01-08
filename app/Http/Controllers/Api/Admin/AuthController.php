<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use GeneralTrait;

    // isAdmin => admins or users
    public $isAdmin;

    // auth => admin_api or user_api
    public $auth;

    public function __construct(Request $request)
    {
        if($request->isAdmin){
            $this->isAdmin = 'admins';
            $this->auth = 'admin_api';
        }else{
            $this->isAdmin = 'users';
            $this->auth = 'user_api';
        }

        $this->middleware("auth:$this->auth",
            ['except' => ['login', 'register']]);

    }
    public function login(Request $request)
    {
        // validation
        try {
            $rules = [
                'email' => [
                    'required',
                    "exists:$this->isAdmin"
                ],
                'password' => ['required'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator -> fails()){
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            // login
            $credentials = $request->only(['email','password']);

            // $token = Auth::guard($request->isAdmin ? 'admin_api' : 'user_api')->attempt($credentials);
            $token = Auth::guard($this->auth)->attempt($credentials);

            if (!$token)
                return $this->returnError('E001', 'بيانات الدخول غير صحيحة');

            // return token
            // $user = Auth::guard($request->isAdmin ? 'admin_api' : 'user_api')->user();
            $user = Auth::guard($this->auth)->user();
            $user->token = $token;

            return $this->returnData($user);
        }catch(\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function register(Request $request){
        try{
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    "unique:$this->isAdmin"
                ],
                'password' => ['required','string','min:6'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];

            $user = null;
            $token = null;

            if($request->isAdmin){
                $user = Admin::create($data);
                $token = Auth::guard('admin_api')->login($user);
            }else {
                $user = User::create($data);
                $token = Auth::guard('user_api')->login($user);
            }

            $user->token = $token;

            return $this->returnData($user, 'User created successfully');
        }catch(\Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function logout()
    {
        try{
            auth()->invalidate();
            return $this->returnSuccessMessage("Successfully logged out");
        }catch(\Exception $ex){
            return $this->returnError('', 'something went wrong');
        }
    }

    public function refresh()
    {
        try{
            $user = auth()->user();
            $token = auth()->refresh();
            $data = [
                'user' => $user,
                'token' => $token,
            ];
            return $this->returnData($data);
        }catch(\Exception $ex){
            return $this->returnError('', 'something went wrong');
        }
    }
}
