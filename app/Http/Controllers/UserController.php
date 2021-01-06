<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * The method that update user account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        // validate request attributes
        $attrs = $request->validate([
            'name' => 'string',
            'password' => 'string|confirmed'
        ]);

        // update user info
        current_user()->update($attrs);

        // return response success with the new user object
        return response_success(current_user());
    }

    /**
     * The method that delete user account
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        // get the password
        $password = $request->password;

        // check if the user provide the right password
        if(!current_user()->validate_password($password)) {
            // return unauthorized response
            return response_unauthorized_401(response_message('The password provided does not match the record we have.'));
        }

        try {
            // delete the user
            current_user()->delete();
        } catch (Exception $e) {
            // return server side error
            return response_internal_server_error(response_message('Unable to delete user account'));
        }

        // return success response
        return response_success(response_message('Delete operation was successful'));
    }

    /**
     * the method that return a user profile
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        // return response as json object
        return response_success(current_user());
    }
}
