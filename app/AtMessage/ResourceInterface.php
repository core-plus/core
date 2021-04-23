<?php

namespace Core\AtMessage;

interface ResourceInterface
{
    /**
     * Get the resourceable type.
     * @return string
     */
    public function type(): string;

    /**
     * Get the resourceable id.
     * @return int
     */
    public function id(): int;

    /**
     * Get the resource push message.
     * @return string
     */
    public function message(): string;
}
