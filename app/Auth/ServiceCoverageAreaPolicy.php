<?php

namespace MakerMaker\Auth;

use \App\Models\User;
use TypeRocket\Models\AuthUser;
use TypeRocket\Auth\Policy;

class ServiceCoverageAreaPolicy extends Policy
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
