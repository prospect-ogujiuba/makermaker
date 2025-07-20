<?php

namespace MakerMaker\Auth;

use \App\Models\User;
use TypeRocket\Models\AuthUser;
use TypeRocket\Auth\Policy;

class ClientPolicy extends Policy
{
    public function update(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_clients');
    }

    public function create(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_clients');
    }

    public function read(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_clients');
    }

    public function destroy(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_clients');
    }
}
