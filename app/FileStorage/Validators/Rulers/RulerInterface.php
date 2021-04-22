<?php

namespace Core\FileStorage\Validators\Rulers;

interface RulerInterface
{
    /**
     * Rule handler.
     * @param array $params
     * @return bool
     */
    public function handle(array $params): bool;
}
