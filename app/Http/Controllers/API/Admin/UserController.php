<?php

namespace App\Http\Controllers\API\Admin;

use App\Actions\Admin\CreateUserAction;
use App\Actions\Admin\GetUsersAction;
use App\Actions\Admin\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(
        private CreateUserAction $createUserAction,
        private UpdateUserAction $updateUserAction,
        private GetUsersAction $getUsersAction
    ) {}

    /**
     * Lista Todos os Usuários do Sistema
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'role', 'department', 'is_active', 'search', 'order_by', 'order_direction'
        ]);

        $users = $this->getUsersAction->execute($filters);

        return UserResource::collection($users);
    }

    /**
     * Cria um Novo Usuário no Sistema
     */
    public function store(CreateUserRequest $request)
    {
        $user = $this->createUserAction->execute($request->validated());

        return new UserResource($user);
    }

    /**
     * Busca Informações de um Usuário Específico
     */
    public function show(User $user)
    {
        $user->loadCount(['travelRequests', 'approvedTravelRequests']);
        
        return new UserResource($user);
    }

    /**
     * Atualiza Informações de um Usuário Específico
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $updatedUser = $this->updateUserAction->execute($user, $request->validated());

        return new UserResource($updatedUser);
    }

    /**
     * Desativa um Usuário do Sistema
     */
    public function destroy(User $user)
    {
        // Soft delete to maintain data integrity
        $user->update(['is_active' => false]);
        
        return response()->json([
            'message' => 'Usuário desativado com sucesso',
            'user' => new UserResource($user)
        ]);
    }
}
