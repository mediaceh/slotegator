<?php

namespace Meals\Application\Component\Provider;

use DateTimeImmutable;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollResult;

interface PollResultProviderInterface
{
    public function getPollResult(
        Poll $poll,
        Dish $dish,
        Employee $employee,
        DateTimeImmutable $dateTime
    ): PollResult;
}
