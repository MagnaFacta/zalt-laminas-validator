<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator
 */

namespace Zalt\Validator;

/**
 * @package    Zalt
 * @subpackage Validator
 * @since      Class available since version 1.0
 */
class IntegerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider IsValidProvider
     */
    public function testIsValid($value)
    {
        $validator = new Integer();
        $result = $validator->isValid($value);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider IsInValidProvider
     */
    public function testIsInValid($value, string $expectedMessageKey)
    {
        $validator = new Integer();
        $result = $validator->isValid($value);
        $this->assertFalse($result);
        $messages = $validator->getMessages();
        $this->assertArrayHasKey($expectedMessageKey, $messages);
    }

    public static function IsValidProvider()
    {
        return [
            ['0'],
            [0],
            ['123456'],
            ['-123456'],
            [123456],
            [-123456],
            [123.0],
            [-123.0],
        ];
    }

    public static function IsInValidProvider()
    {
        return [
            ['', Integer::STRING_EMPTY],
            [' ' , Integer::NOT_INTEGER],
            ['text', Integer::NOT_INTEGER],
            ['123.0', Integer::NOT_INTEGER],
            ['-123.0', Integer::NOT_INTEGER],
            [' 123', Integer::NOT_INTEGER],
            ['123 ', Integer::NOT_INTEGER],
            ['123-', Integer::NOT_INTEGER],
            ['123-456', Integer::NOT_INTEGER],
            ['-123-456', Integer::NOT_INTEGER],
            [123.1, Integer::NOT_INTEGER],
            [-123.1, Integer::NOT_INTEGER],
            ['123.0', Integer::NOT_INTEGER],
            ['-123.0', Integer::NOT_INTEGER],
            [true, Integer::INVALID],
            [new \DateTime(), Integer::INVALID],
            [new \stdClass(), Integer::INVALID],
        ];
    }

}
