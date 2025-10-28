<?php

namespace App\Http\Controllers;

use App\Dto\Common\FilterableTablePagePropsData;
use App\Dto\Common\FiltersData;
use App\Enums\UserRole;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Services\UserService;
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
}
