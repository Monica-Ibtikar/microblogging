<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
   
    public function signup(SignupRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' =>  $request->file('image')->store('avatars')
        ]);

        $token = $user->createToken('auth-token');
        
        Mail::to($user->email)->send(new WelcomeMail('welcome to aour system'));
        
        return response()->json([
            'user' => $user,
            'auth_token' => $token->plainTextToken
        ]);
    }

    
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => __('lang.invalid_credientials'),
            ]);
        }

        return response()->json([
            'auth_token' =>  $user->createToken('auth-token')->plainTextToken
        ]);
    }

    
    public function follow(User $user,Request $request)
    {
        try {
            $currentUser = $request->user();
            $followings = $currentUser->followings();
            if (app(UserRepository::class)->isUserInFollowings($user->id, $followings)) {
                return response()->json([
                    'message' => __('lang.followed_already', ['name' => $user->name])
                ],Response::HTTP_NOT_FOUND);
            }
            $followings->attach($user);
            return response()->json([
                'message' =>  __('lang.followed_successfully', ['name' => $user->name])
            ]);
        } catch (NotFoundHttpException $exception) {
           return response()->json('Not found user')->status(Response::HTTP_NOT_FOUND);
        }
    }
}
