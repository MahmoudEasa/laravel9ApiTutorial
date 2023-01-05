<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use GeneralTrait;
    public function login(Request $request)
    {
        // validation
        try {
            $rules = [
                'email' => ['required','exists:admins,email'],
                'password' => ['required'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator -> fails()){
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            // login
            $credentials = $request->only(['email','password']);
            $token = Auth::guard('admin_api')->attempt($credentials);
            $user = Auth::guard('admin_api')->user();
            $user->token = $token;

            if (!$token)
                return $this->returnError('E001', 'بيانات الدخول غير صحيحة');

            // return token
            return $this->returnData($user);
        }catch(\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}