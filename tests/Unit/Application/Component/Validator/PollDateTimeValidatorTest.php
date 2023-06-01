<?php

namespace tests\Meals\Unit\Application\Component\Validator;

use DateTimeImmutable;
use Meals\Application\Component\Validator\Exception\PollDateTimeIsNotAllowedException;
use Meals\Application\Component\Validator\PollDateTimeValidator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PollDateTimeValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful()
    {
        $dateTime = $this->prophesize(DateTimeImmutable::class);
        $dateTime->format('w')->willReturn('1');
        $dateTime->format('G')->willReturn('12');

        $validator = new PollDateTimeValidator();
        verify($validator->validate($dateTime->reveal()))->null();
    }

    public function testFail()
    {
        $this->expectException(PollDateTimeIsNotAllowedException::class);

        $dateTime = $this->prophesize(DateTimeImmutable::class);
        $dateTime->format('w')->willReturn('4');
        $dateTime->format('G')->willReturn('12');

        $validator = new PollDateTimeValidator();
        $validator->validate($dateTime->reveal());
    }
}
