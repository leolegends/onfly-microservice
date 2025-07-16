<?php

namespace App\Http\Controllers\API\User;

use App\Actions\User\ChangePasswordAction;
use App\Actions\User\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\User\UserProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Busca Informações do Perfil do Usuário
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => new UserProfileResource($user),
        ]);
    }

    /**
     * Atualiza Informações do Perfil do Usuário
     */
    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        try {
            $user = $action->execute($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso.',
                'data' => new UserProfileResource($user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar perfil: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Altera Senha do Usuário
     */
    public function changePassword(ChangePasswordRequest $request, ChangePasswordAction $action): JsonResponse
    {
        try {
            $action->execute($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar senha: ' . $e->getMessage(),
            ], 500);
        }
    }
}
