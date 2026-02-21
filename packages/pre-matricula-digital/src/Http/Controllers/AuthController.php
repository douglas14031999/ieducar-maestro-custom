<?php

namespace iEducar\Packages\PreMatricula\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @codeCoverageIgnore
 */
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('login');
    }

    /**
     * Return authenticated user.
     *
     *
     * @return array
     */
    public function check(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        if (empty($user)) {
            abort(401);
        }

        if (!$user->isAdmin() && empty($user->menu()->where('process', 5656)->exists())) {
            abort(401);
        }

        $user->load('schools');

        return [
            'name' => $user->person->nome,
            'level' => $user->type->nivel,
            'schools' => $user->schools->pluck('cod_escola'),
        ];
    }

    /**
     * Return authenticated user.
     *
     *
     * @return array
     */
    public function login(Request $request)
    {
        if (app()->environment('local')) {
            /** @var User $user */
            $user = Auth::loginUsingId(config('prematricula.user', 1));

            $user->load('schools');

            return [
                'name' => $user->person->nome,
                'level' => $user->type->nivel,
                'schools' => $user->schools->pluck('cod_escola'),
            ];
        }

        return $this->check($request);
    }
}
