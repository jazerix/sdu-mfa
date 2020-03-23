<?php

namespace SDU\MFA\Protection;

use SDU\MFA\Azure\User;

class IsAll extends Filter
{
    public function access(User $user) : bool
    {
        return $user->is($this->roles);
    }
}