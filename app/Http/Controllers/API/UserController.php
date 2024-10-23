<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    private $relations = [
        'accounts.transactions',
        'budgets.budgetTransactions',
        'budgets.category',
        'goals.goalTransactions',
        'categories.transactions',
        'categories.recurringTransactions',
    ];

    public function index(){
        $users = User::with($this->relations)->get();
        return response()->json([
            'data' => $users,
            'message' => 'success',
            'code' => 200
        ], 200);
    }
    
    public function show(){
        $user = User::with($this->relations)->find(Auth::user()->id);
        return response()->json([
            'data' => $user,
            'message' => 'success',
            'code' => 200
        ], 200);
    }

    public function fetch(Request $request)
    {
        return response()->json([
            'data' => $request->user()->load($this->relations),
            'message' => 'Data profile user berhasil diambil',
            'code' => 200,
        ], 200);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'code' => 200,
                ], 200);
            }

            $user = User::where('email', $request->email)->first();
            if ( ! Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $user->load($this->relations);

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                    'message' => 'Authenticated',
                    'code' => 200
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                    'message' => 'Something went wrong',
                    'error' => $error,
                    'status' => 'Authentication Failed',
                    'code' => 500,
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return response()->json([
            'data' => $token,
            'mesage' => 'Token Revoked, Berhasil Logout',
            'code' => 200
        ], 200);
    }

    public function detail(){
        $user = User::where('id', Auth::user()->id)->with($this->relations)->first();
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
                'status' => 'Success',
                'code' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $user = User::findOrFail($id);

            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user->load($this->relations)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
