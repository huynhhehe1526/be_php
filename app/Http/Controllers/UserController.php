<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //tạo thông tin người dùng
    function register(Request $req)
    {
        $user = new User;
        $user->userName = $req->input('userName');
        $user->password = Hash::make($req->input('password'));
        $user->fullName = $req->input('fullName');
        $user->email = $req->input('email');
        $user->address = $req->input('address');
        $user->phoneNumber = $req->input('phoneNumber');
        $user->roleId = $req->input('roleId');
        $user->typeAccount = 'normal';
        $user->save();
        return $user;
    }
    //login
    function login(Request $req)
    {
        //tìm kiếm user thông qua email
        $user = User::where('email', $req->email)->first();
        if (!$user) {
            return ["error" => "Email not registered or does not exit"];
        }
        if ($user && !Hash::check($req->password, $user->password)) {
            return ["error" => "Password was wrong"];
        }
        return $user;
    }
    function show()
    {
        $user = User::all();
        return $user;
    }
    // function destroy($id)
    // {
    //     $result = User::where('id', $id)->delete();
    //     if ($result) {
    //         return ["result" => "Delete user succeed!"];
    //     } else {
    //         return ["result" => "Delete user failed!"];
    //     }
    //     return $result;
    // }

    function destroy($id)
    {
        $result = User::where('id', $id)->delete();
        if ($result) {
            return ["result" => "Delete user succeed!"];
        } else {
            $checkDeleted = User::where('id', $id)->first();
            if ($checkDeleted === null) {
                return ["result" => "Delete user succeed!"];
            } else {
                return ["result" => "Delete user failed!"];
            }
        }
    }

    function addUser(Request $req)
    {
        $user = new User;
        $exitingUser = User::where('email', $req->input('email'))->first();
        if ($exitingUser) {
            return ["error" => "Email is exist"];
        } else {
            $user->userName = $req->input('userName');
            $user->password = Hash::make($req->input('password'));
            $user->fullName = $req->input('fullName');
            $user->email = $req->input('email');
            $user->address = $req->input('address');
            $user->phoneNumber = $req->input('phoneNumber');
            $user->gender = $req->input('gender');
            $user->roleId = $req->input('roleId');
            $user->typeAccount = 'normal';

            $user->save();
        }

        return $user;
    }


    function updateData(Request $data)
    {
        try {
            // Check for the mandatory parameters
            if (!isset($data['id'])) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Missing the parameters!!'
                ];
            }

            // Find user by id
            $user = User::find($data['id']);

            if ($user) {
                // Update user information with selective checks
                if (isset($data['userName']) && $data['userName'] !== $user->userName) {
                    $user->userName = $data['userName'];
                }

                if (isset($data['password']) && $data['password'] !== 'Hashcode') {
                    $user->password = Hash::make($data['password']);
                }

                if (isset($data['fullName']) && $data['fullName'] !== $user->fullName) {
                    $user->fullName = $data['fullName'];
                }

                if (isset($data['address']) && $data['address'] !== $user->address) {
                    $user->address = $data['address'];
                }

                if (isset($data['phoneNumber']) && $data['phoneNumber'] !== $user->phoneNumber) {
                    $user->phoneNumber = $data['phoneNumber'];
                }

                if (isset($data['gender']) && $data['gender'] !== $user->gender) {
                    $user->gender = $data['gender'];
                }

                if (isset($data['roleId']) && $data['roleId'] !== $user->roleId) {
                    $user->roleId = $data['roleId'];
                }

                $user->save();

                return [
                    'errCode' => 0,
                    'message' => 'Update user succeed!'
                ];
            } else {
                return [
                    'errCode' => 1,
                    'message' => 'User not found'
                ];
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}