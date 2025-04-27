<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Exception;
use Throwable;

class UserController extends Controller
{
    public function viewLogin(Request $request)
    {
        return view('index');
    }

    public function viewRegister(Request $request)
    {
        return view('register');
    }

    public function login(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (empty($user))
                throw new Exception("No existe un usuario registrado con ese correo", 400);

            $logged = Hash::check($request->password, $user->password);

            if (!$logged)
                throw new Exception("Las credenciales no son correctas", 400);

            $token = Auth::login($user);

            DB::commit();

            return response()->json([
                "message" => "Inicio sesión correctamente.",
                "data" => [
                    "user" => $user,
                    "access" => [
                        "token" => $token,
                        // "expires" => Auth::factory()->getTTL() * 60
                    ],
                ]
            ], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            $code = $e->getCode() ?: 400;
            return response()->json([
                "message" => $e->getMessage(),
                "code" => $code,
            ], $code);
        }
    }


    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!empty($user))
                throw new Exception("Ya existe un usuario registrado", 400);

            $user = new User();
            $user->name = $request->name;
            $user->lastName = $request->lastName;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();

            return response()->json([
                "message" => "Registro exitoso",
                "data" => $user
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                "code" => $e->getCode(),
            ], 400);
        }
    }
}
