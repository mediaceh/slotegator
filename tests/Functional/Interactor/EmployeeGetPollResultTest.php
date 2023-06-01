<?php

namespace tests\Meals\Functional\Interactor;

use DateTimeImmutable;
use Meals\Application\Component\Validator\Exception\AccessDeniedException;
use Meals\Application\Component\Validator\Exception\PollDateTimeIsNotAllowedException;
use Meals\Application\Component\Validator\Exception\PollHasNoDishException;
use Meals\Application\Component\Validator\Exception\PollIsNotActiveException;
use Meals\Application\Feature\Poll\UseCase\EmployeeGetsPollResult\Interactor;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Dish\DishList;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Menu\Menu;
use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollResult;
use Meals\Domain\User\Permission\Permission;
use Meals\Domain\User\Permission\PermissionList;
use Meals\Domain\User\User;
use tests\Meals\Functional\Fake\Provider\FakeDishProvider;
use tests\Meals\Functional\Fake\Provider\FakeEmployeeProvider;
use tests\Meals\Functional\Fake\Provider\FakePollProvider;
use tests\Meals\Functional\Fake\Provider\FakePollResultProvider;
use tests\Meals\Functional\FunctionalTestCase;

class EmployeeGetPollResultTest extends FunctionalTestCase
{
    public function testSuccessful()
    {
        $pollResult = $this->performTestMethod(
            $this->getPoll(true),
            $this->getEmployeeWithPermissions(),
            $this->getDishIsInMenu(),
            $this->getAllowedPollDateTime(),
            $this->getPollResult(
                $this->getPoll(true),
                $this->getEmployeeWithPermissions(),
                $this->getDishIsInMenu()
            )
        );
        verify($pollResult)->equals($pollResult);
    }

    public function testUserHasNotPermissions()
    {
        $this->expectException(AccessDeniedException::class);

        $pollResult = $this->performTestMethod(
            $this->getPoll(true),
            $this->getEmployeeWithNoPermissions(),
            $this->getDishIsInMenu(),
            $this->getAllowedPollDateTime(),
            $this->getPollResult(
                $this->getPoll(true),
                $this->getEmployeeWithNoPermissions(),
                $this->getDishIsInMenu()
            )
        );
        verify($pollResult)->equals($pollResult);
    }

    public function testPollIsNotActive()
    {
        $this->expectException(PollIsNotActiveException::class);

        $pollResult = $this->performTestMethod(
            $this->getPoll(false),
            $this->getEmployeeWithPermissions(),
            $this->getDishIsInMenu(),
            $this->getAllowedPollDateTime(),
            $this->getPollResult(
                $this->getPoll(false),
                $this->getEmployeeWithPermissions(),
                $this->getDishIsInMenu()
            )
        );
        verify($pollResult)->equals($pollResult);
    }

    public function testDayOfWeekIsNotAllowed()
    {
        $this->expectException(PollDateTimeIsNotAllowedException::class);

        $pollResult = $this->performTestMethod(
            $this->getPoll(false),
            $this->getEmployeeWithPermissions(),
            $this->getDishIsInMenu(),
            $this->getNotAllowedPollDayOfWeek(),
            $this->getPollResult(
                $this->getPoll(true),
                $this->getEmployeeWithPermissions(),
                $this->getDishIsInMenu()
            )
        );
        verify($pollResult)->equals($pollResult);
    }

    public function testTimeIsNotAllowed()
    {
        $this->expectException(PollDateTimeIsNotAllowedException::class);

        $pollResult = $this->performTestMethod(
            $this->getPoll(false),
            $this->getEmployeeWithPermissions(),
            $this->getDishIsInMenu(),
            $this->getNotAllowedPollTime(),
            $this->getPollResult(
                $this->getPoll(true),
                $this->getEmployeeWithPermissions(),
                $this->getDishIsInMenu()
            )
        );
        verify($pollResult)->equals($pollResult);
    }

    public function testPollHasNoDish()
    {
        $this->expectException(PollHasNoDishException::class);

        $pollResult = $this->performTestMethod(
            $this->getPoll(false),
            $this->getEmployeeWithPermissions(),
            $this->getDishIsNotInMenu(),
            $this->getAllowedPollDateTime(),
            $this->getPollResult(
                $this->getPoll(true),
                $this->getEmployeeWithPermissions(),
                $this->getDishIsNotInMenu()
            )
        );
        verify($pollResult)->equals($pollResult);
    }

    private function performTestMethod(
        Poll $poll,
        Employee $employee,
        Dish $dish,
        DateTimeImmutable $dateTime,
        PollResult $pollResult
    ): PollResult
    {
        $this->getContainer()->get(FakeEmployeeProvider::class)->setEmployee($employee);
        $this->getContainer()->get(FakePollProvider::class)->setPoll($poll);
        $this->getContainer()->get(FakeDishProvider::class)->setPollDish($dish);
        $this->getContainer()->get(FakePollResultProvider::class)->setPollResult($pollResult);

        return $this->getContainer()->get(Interactor::class)->getPollResult(
            $poll->getId(),
            $employee->getId(),
            $dish->getId(),
            $dateTime
        );
    }

    private function getEmployeeWithPermissions(): Employee
    {
        return new Employee(
            1,
            $this->getUserWithPermissions(),
            4,
            'Surname'
        );
    }

    private function getUserWithPermissions(): User
    {
        return new User(
            1,
            new PermissionList(
                [
                    new Permission(Permission::VIEW_ACTIVE_POLLS),
                ]
            ),
        );
    }

    private function getEmployeeWithNoPermissions(): Employee
    {
        return new Employee(
            1,
            $this->getUserWithNoPermissions(),
            4,
            'Surname'
        );
    }

    private function getUserWithNoPermissions(): User
    {
        return new User(
            1,
            new PermissionList([]),
        );
    }

    private function getPoll(bool $active): Poll
    {
        return new Poll(
            1,
            $active,
            new Menu(
                1,
                'title',
                $this->getDishList(),
            )
        );
    }

    private function getPollResult(Poll $poll, Employee $employee, Dish $dish): PollResult
    {
        return new PollResult(1, $poll, $employee, $dish, $employee->getFloor());
    }

    private function getDishList(): DishList
    {
        return new DishList(
            $this->getDishes()
        );
    }

    private function getDishes(): array
    {
        return [
            $this->getDishIsInMenu(),
        ];
    }

    private function getDishIsInMenu(): Dish
    {
        return new Dish(
            1,
            'Dish',
            'Perfect dish'
        );
    }

    private function getDishIsNotInMenu(): Dish
    {
        return new Dish(
            3,
            'Dish is not in menu',
            'Perfect dish is not in menu'
        );
    }

    private function getAllowedPollDateTime(): DateTimeImmutable
    {
        return (new DatetimeImmutable('last monday'))->setTime(12, 0);
    }

    private function getNotAllowedPollTime(): DateTimeImmutable
    {
        return (new DatetimeImmutable('last monday'))->setTime(4, 0);
    }

    private function getNotAllowedPollDayOfWeek(): DateTimeImmutable
    {
        return (new DatetimeImmutable('last wednesday'))->setTime(12, 0);
    }
}
