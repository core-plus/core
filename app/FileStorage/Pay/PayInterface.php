<?php

namespace Core\FileStorage\Pay;

interface PayInterface
{
    /**
     * Get paid status.
     * @return bool
     */
    public function getPaid(): bool;

    /**
     * Get file paid node amount.
     * @return int
     */
    public function getAmount(): int;

    /**
     * Get pay node.
     * @return string
     */
    public function getNode(): string;

    /**
     * Get pay type.
     * @return string
     */
    public function getType(): string;
}
