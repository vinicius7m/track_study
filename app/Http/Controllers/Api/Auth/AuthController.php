<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\Api\Auth\AuthService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request->validated());
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->authService->logout($request->user());
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->authService->profile($request->user());
    }

    public function forgetPassword(Request $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->get();

            if (count($user) > 0) {
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain.'/reset-password?token='.$token;

                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = 'Criar senha nova';
                $data['body'] = 'Por favor click abaixo no link para criar uma nova senha.';

                Mail::send('forget-password-mail', ['data' => $data], function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['title']);
                });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime,
                    ]
                );

                return response()->json(['success' => true, 'message' => 'Por favor, verifique o seu e-mail para criar uma nova senha.']);
            } else {
                return response()->json(['success' => false, 'error' => 'Usuário não encontrado!']);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function resetPasswordLoad(Request $request)
    {
        $resetData = PasswordReset::where('token', $request->token)->get();

        if(isset($request->token) && count($resetData) > 0) {
            $user = User::where('email', $resetData[0]['email'])->get();

            return view('reset-password', compact('user'));
        } else {
            return view('404');
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::find($request->id);
        $user->password = bcrypt($request->password);
        $user->save();

        PasswordReset::where('email', $user->email)->delete();

        return "<h1>Sua nova senha foi criada com sucesso.</h1>";

    }
}
