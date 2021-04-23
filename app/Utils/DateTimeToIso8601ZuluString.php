<?php

namespace Core\Utils;

use Carbon\Carbon;

trait DateTimeToIso8601ZuluString
{
    /**
     * DateTime to ISO 8601 Zulu time.
     *
     * @param {\DateTime|string|null} $dateTime
     * @return string
     */
    protected function dateTimeToIso8601ZuluString($dateTime = null): ?string
    {
        if (is_null($dateTime) || empty($dateTime)) {
            return null;
        } elseif (! ($dateTime instanceof Carbon)) {
            $dateTime = new Carbon($dateTime);
        }

        return $dateTime->toIso8601ZuluString();
    }
}
