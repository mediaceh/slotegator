<?php

namespace tests\Meals\Functional\Fake\Provider;

use Meals\Application\Component\Provider\PollResultProviderInterface;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollResult;
use DateTimeImmutable;

class FakePollResultProvider implements PollResultProviderInterface
{
    /** @var PollResult */
    private $pollResult;

    public function setPollResult(PollResult $pollResult): void
    {
        $this->pollResult = $pollResult;
    }
    public function getPollResult(Poll $poll, Dish $dish, Employee $employee, DateTimeImmutable $dateTime): PollResult
    {
        return $this->pollResult;
    }
}
