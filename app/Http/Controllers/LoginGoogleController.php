<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Two\GoogleProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class LoginGoogleController extends Controller
{
    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }



    /**
 
     * Create a new controller instance.
 
     *
 
     * @return void
 
     */
    public function handleGoogleCallback()
    {
        try {

            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->google_id)->first();

            if ($finduser) {

                Auth::login($finduser);

                return redirect()->intended('/');
            } else {
                $newUser = User::create([
                    'fullName' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => encrypt('12345678'),
                    'typeAccount' => 'Google',
                    'roleId' =>'R3'
                ]);

                Auth::login($newUser);

                return redirect()->intended('/');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    // public function redirectToAuth(): JsonResponse
    // {
    //     return response()->json([
    //         'url' => Socialite::driver('google')
    //             ->stateless()
    //             ->redirect()
    //             ->getTargetUrl(),
    //     ]);
    // }

    // public function handleAuthCallback(): JsonResponse
    // {
    //     try {
    //         /** @var SocialiteUser $socialiteUser */
    //         $socialiteUser = Socialite::driver('google')->user();
    //     } catch (ClientException $e) {
    //         return response()->json(['error' => 'Invalid credentials provided.'], 422);
    //     }
    //     /** @var User $user */
    //     $user = User::query()

    //         ->firstOrCreate(
    //             [
    //                 'email' => $socialiteUser->getEmail(),
    //             ],
    //             [
    //                 'password' => encrypt('12345678'),
    //                 'fullName' => $socialiteUser->getName(),
    //                 'google_id' => $socialiteUser->getId(),
    //                 'typeAccount' => 'Google',

    //             ]
    //         );
    //     return response()->json([
    //         'user' => $user,
    //         'access_token' => $user->createToken('google-token')->plainTextToken,
    //         'token_type' => 'Bearer',
    //     ]);
    // }
}