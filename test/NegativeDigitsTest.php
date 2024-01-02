<?php

declare(strict_types=1);

namespace Zalt\Validator;

use PHPUnit\Framework\TestCase;

class NegativeDigitsTest extends TestCase
{
    public static function correctNumbersProvider(): array
    {
        return [
            [123],
            ['234'],
            [-123],
            ['-234'],
        ];
    }

    public static function incorrectNumbersProvider(): array
    {
        return [
            [123.45],
            ['345.45'],
            [-123.45],
            ['-345.45'],
            ['1s2d3'],
            ['-2a3b4'],
            ['1a2b3c.d4e5f'],
            ['-a3a4b5.45'],
            ['0-abc1'],
            ['----abde12'],
            ['--23']
        ];
    }

    /**
     * @test
     * @dataProvider incorrectNumbersProvider
     */
    public function invalidNumbersAreCorrectlyDetectedAsInvalid(float|int|string $input): void
    {
        $negativeDigitsFilter = new NegativeDigits();

        $isValid = $negativeDigitsFilter->isValid($input);

        $this->assertFalse($isValid, "Failed asserting that '$input' is invalid");
    }

    /**
     * @test
     * @dataProvider correctNumbersProvider
     */
    public function validNumbersAreCorrectlyDetectedAsValid(float|int|string $input): void
    {
        $negativeDigitsFilter = new NegativeDigits();

        $isValid = $negativeDigitsFilter->isValid($input);

        $this->assertTrue($isValid, "Failed asserting that '$input' is valid");
    }
}