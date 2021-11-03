<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
    //login con sanctum
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()
                ->json(['data' => $user,'access_token' => $token, 'token_type' => 'Bearer', ]);
        }else{
            return response()->json(['error' => 'No Autorizado'], 401);
        }
    }

    // method for user logout and delete token
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}