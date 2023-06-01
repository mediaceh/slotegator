<?php

namespace Meals\Application\Component\Validator;

use DateTimeImmutable;
use Meals\Application\Component\Validator\Exception\PollDateTimeIsNotAllowedException;

class PollDateTimeValidator
{
    public function validate(DateTimeImmutable $dateTime): void
    {
        if (
            $dateTime->format('w') != 1
            || $dateTime->format('G') < 6
            || $dateTime->format('G') >= 22
        ) {
            throw new PollDateTimeIsNotAllowedException();
        }
    }
}
