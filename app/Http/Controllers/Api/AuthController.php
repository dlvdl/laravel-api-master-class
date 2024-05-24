<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiLoginRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponses;
    public function login(ApiLoginRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'API token for' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth())->plainTextToken
            ]
        );
    }

    public function register() {
        return  $this->ok('Ok');
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return $this->ok('');
    }
}
