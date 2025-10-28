<?php

namespace App\Http\Controllers;

use App\Dto\Common\FilterableTablePagePropsData;
use App\Dto\Common\FiltersData;
use App\Dto\User\CreateUserData;
use App\Dto\User\UpdateUserData;
use App\Dto\User\UserData;
use App\Enums\UserRole;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\DestroyUserRequest;
use App\Http\Requests\User\EditUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(IndexUserRequest $request): Response
    {
        $workshopId = auth()->user()->workshop_id;
        $usersPaginated = $this->userService->getUsers($workshopId);

        $props = FilterableTablePagePropsData::from([
            'tableData' => $usersPaginated,
            'filters' => FiltersData::from([]),
        ]);

        return Inertia::render('users/index', $props);
    }

    public function create(CreateUserRequest $request): Response
    {
        return Inertia::render('users/create', [
            'roles' => UserRole::options(),
        ]);
    }

    public function edit(EditUserRequest $request, User $user): Response
    {
        $user->load('roles');

        return Inertia::render('users/edit', [
            'user' => UserData::fromModel($user),
            'roles' => UserRole::options(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $userData = CreateUserData::from($request->validated());

        $this->userService->create($userData);

        return redirect()
            ->route('users.index')
            ->with('success', 'Użytkownik został dodany');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $updateData = UpdateUserData::from($request->validated());

        $this->userService->update($user, $updateData);

        return redirect()
            ->route('users.index')
            ->with('success', 'Użytkownik został zaktualizowany');
    }

    public function destroy(DestroyUserRequest $request, User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return redirect()
                ->back()
                ->with('error', __('users.cannot_delete_self'));
        }

        $this->userService->delete($user);

        return redirect()
            ->route('users.index')
            ->with('success', __('users.user_deleted'));
    }
}
