<?php

namespace SDU\MFA\Protection;

use SDU\MFA\Azure\User;

class IsOne extends Filter
{
    public function access(User $user) : bool
    {
        return count(
                array_intersect(
                    $this->roles(),
                    $user->roles()->names())
            ) > 0;
    }
}