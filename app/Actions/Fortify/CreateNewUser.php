<?php

namespace App\Actions\Fortify;

use App\Dto\Workshop\RegisterWorkshopData;
use App\Models\User;
use App\Services\Auth\WorkshopRegistrationService;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function __construct(
        protected WorkshopRegistrationService $registrationService
    ) {}

    public function create(array $input): User
    {
        $data = RegisterWorkshopData::validateAndCreate($input);

        return $this->registrationService->register($data);
    }
}
