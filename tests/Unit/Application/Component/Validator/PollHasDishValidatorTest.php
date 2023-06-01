<?php

namespace tests\Meals\Unit\Application\Component\Validator;

use Meals\Application\Component\Validator\Exception\PollHasNoDishException;
use Meals\Application\Component\Validator\PollHasDishValidator;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Dish\DishList;
use Meals\Domain\Menu\Menu;
use Meals\Domain\Poll\Poll;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PollHasDishValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful()
    {
        $dish = $this->prophesize(Dish::class);

        $dishList = $this->prophesize(DishList::class);
        $dishList->hasDish($dish)->willReturn(true);

        $menu = $this->prophesize(Menu::class);
        $menu->getDishes()->willReturn($dishList->reveal());

        $poll = $this->prophesize(Poll::class);
        $poll->getMenu()->willReturn($menu->reveal());

        $validator = new PollHasDishValidator();
        verify($validator->validate($poll->reveal(), $dish->reveal()))->null();
    }

    public function testFail()
    {
        $this->expectException(PollHasNoDishException::class);

        $dish = $this->prophesize(Dish::class);

        $dishList = $this->prophesize(DishList::class);
        $dishList->hasDish($dish)->willReturn(false);

        $menu = $this->prophesize(Menu::class);
        $menu->getDishes()->willReturn($dishList->reveal());

        $poll = $this->prophesize(Poll::class);
        $poll->getMenu()->willReturn($menu->reveal());

        $validator = new PollHasDishValidator();
        verify($validator->validate($poll->reveal(), $dish->reveal()));
    }
}
