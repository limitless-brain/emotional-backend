<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

/*
|--------------------------------------------------------------------------
| Authorization Controller
|--------------------------------------------------------------------------
| This class contains api methods that handles user
| lifecycle.
|
*/

class AuthController extends Controller
{
    /**
     * the method that authorize a user to use API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // validate request data
        $attrs = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        // authentication credentials
        $credentials = [
            'email' => $attrs['email'],
            'password' => $attrs['password']
        ];

        // authorize user
        if (!auth()->attempt($credentials, $attrs['remember_me'])) {
            // user authorization failed
            // return 401 response
            return response_unauthorized_401(response_message('The submitted data does not match the records we have.'));
        }

        // generate access token
        $tokenResult = current_user()->createToken('Personal Access Token');
        $token = $tokenResult->token;

        // store the token
        $token->save();

        // create cookie, using XSRF-TOKEN the default axios setup
        $cookie = new Cookie("XSRF-TOKEN", "Bearer {$tokenResult->accessToken}");

        // handle remember me
        if ($attrs['remember_me']) {
            // add cookie expires date
            $cookie = $cookie->withExpires(now()->days(TOKEN_EXPIRES_AT));
        }

        // set the cookie to be global
        $cookie = $cookie->withHttpOnly(false);

        // return response as json object
        return response_success(response_message('Successfully logged in the user.'))
            ->cookie($cookie);
    }

    /**
     * the method that de-authorize a user from using API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // revoke user token
        current_user()->token()->revoke();
        // return response as json object
        return response_success(response_message('Successfully logged out the user.'));
    }

    /**
     * the method that add a new user to API database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request): JsonResponse
    {
        // validate request data
        $attrs = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        // encrypt password
        $attrs['password'] = bcrypt($attrs['password']);

        // create user object
        $user = User::create($attrs);

        // create default playlist
        $user->playlists()->save(new Playlist(['name' => 'recently played']));
        $user->playlists()->save(new Playlist(['name' => 'favorite']));

        // return response as json object
        return response_data_created(response_message('Successfully signed up the user.'));
    }
}
