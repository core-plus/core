<?php

namespace Core\FileStorage\Validators;

use Illuminate\Http\Request;

interface ValidatorInterface
{
    /**
     * The validate.
     */
    public function validate(Request $request): void;
}
