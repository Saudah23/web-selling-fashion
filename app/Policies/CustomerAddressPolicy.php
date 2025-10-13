<?php

namespace App\Policies;

use App\Models\CustomerAddress;
use App\Models\User;

class CustomerAddressPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CustomerAddress $customerAddress): bool
    {
        return $user->id === $customerAddress->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CustomerAddress $customerAddress): bool
    {
        return $user->id === $customerAddress->user_id;
    }

    public function delete(User $user, CustomerAddress $customerAddress): bool
    {
        return $user->id === $customerAddress->user_id;
    }
}