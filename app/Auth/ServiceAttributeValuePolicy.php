<?php
namespace MakerMaker\Auth;

use \App\Models\User;
use TypeRocket\Models\AuthUser;
use TypeRocket\Auth\Policy;

class ServiceAttributeValuePolicy extends Policy
{
    public function update(AuthUser $auth, $object)
    {
        return false;
    }

    public function create(AuthUser $auth, $object)
    {
        return false;
    }

    public function read(AuthUser $auth, $object)
    {
        return false;
    }

    public function destroy(AuthUser $auth, $object)
    {
        return false;
    }
}