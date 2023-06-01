<?php

namespace Meals\Application\Feature\Poll\UseCase\EmployeeGetsPollResult;

use DateTimeImmutable;
use Meals\Application\Component\Provider\DishProviderInterface;
use Meals\Application\Component\Provider\PollProviderInterface;
use Meals\Application\Component\Provider\EmployeeProviderInterface;
use Meals\Application\Component\Provider\PollResultProviderInterface;
use Meals\Application\Component\Validator\PollDateTimeValidator;
use Meals\Application\Component\Validator\PollHasDishValidator;
use Meals\Application\Component\Validator\PollIsActiveValidator;
use Meals\Application\Component\Validator\UserHasAccessToViewPollsValidator;
use Meals\Domain\Poll\PollResult;

class Interactor
{
    /** @var EmployeeProviderInterface */
    private $employeeProvider;

    /** @var PollProviderInterface */
    private $pollProvider;

    /** @var PollResultProviderInterface */
    private $pollResultProvider;

    /** @var DishProviderInterface */
    private $dishProvider;

    /** @var UserHasAccessToViewPollsValidator */
    private $userHasAccessToPollsValidator;

    /** @var PollIsActiveValidator */
    private $pollIsActiveValidator;

    /** @var PollHasDishValidator */
    private $pollHasDishValidator;

    /** @var PollDateTimeValidator */
    private $pollDateTimeValidator;

    /**
     * Interactor constructor.
     * @param EmployeeProviderInterface $employeeProvider
     * @param PollProviderInterface $pollProvider
     * @param PollResultProviderInterface $pollResultProvider
     * @param DishProviderInterface $dishProvider
     * @param UserHasAccessToViewPollsValidator $userHasAccessToPollsValidator
     * @param PollIsActiveValidator $pollIsActiveValidator
     * @param PollHasDishValidator $pollHasDishValidator
     * @param PollDateTimeValidator $pollDateTimeValidator
     */
    public function __construct(
        EmployeeProviderInterface $employeeProvider,
        PollProviderInterface $pollProvider,
        PollResultProviderInterface $pollResultProvider,
        DishProviderInterface $dishProvider,
        UserHasAccessToViewPollsValidator $userHasAccessToPollsValidator,
        PollIsActiveValidator $pollIsActiveValidator,
        PollHasDishValidator $pollHasDishValidator,
        PollDateTimeValidator $pollDateTimeValidator
    ) {
        $this->employeeProvider = $employeeProvider;
        $this->pollProvider = $pollProvider;
        $this->pollResultProvider = $pollResultProvider;
        $this->dishProvider = $dishProvider;
        $this->userHasAccessToPollsValidator = $userHasAccessToPollsValidator;
        $this->pollIsActiveValidator = $pollIsActiveValidator;
        $this->pollHasDishValidator = $pollHasDishValidator;
        $this->pollDateTimeValidator = $pollDateTimeValidator;
    }

    /**
     * @param int $pollId
     * @param int $employeeId
     * @param int $dishId
     * @param DateTimeImmutable $dateTime
     * @return PollResult
     */
    public function getPullResult(int $pollId, int $employeeId, int $dishId, DateTimeImmutable $dateTime): PollResult
    {
        $employee = $this->employeeProvider->getEmployee($employeeId);
        $poll = $this->pollProvider->getPoll($pollId);
        $dish = $this->dishProvider->getDish($dishId);

        $this->userHasAccessToPollsValidator->validate($employee->getUser());
        $this->pollIsActiveValidator->validate($poll);
        $this->pollHasDishValidator->validate($poll, $dish);
        $this->pollDateTimeValidator->validate($dateTime);

        return $this->pollResultProvider->getPollResult($poll, $dish, $employee, $dateTime);
    }
}
