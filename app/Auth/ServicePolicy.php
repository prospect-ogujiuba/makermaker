<?php

namespace MakerMaker\Auth;

use MakerMaker\Models\User;
use TypeRocket\Models\AuthUser;
use TypeRocket\Auth\Policy;

class ServicePolicy extends Policy
{
    public function update(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_services');
    }

    public function create(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_services');
    }

    public function read(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_services');
    }

    public function destroy(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_services');
    }
}