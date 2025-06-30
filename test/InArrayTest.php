<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator;

use PHPUnit\Framework\TestCase;

/**
 * @package    Zalt
 * @subpackage Validator
 * @since      Class available since version 1.0
 */
class InArrayTest extends TestCase
{
    public static function provideInValids()
    {
        return [
            [null, ['a', 'b', 'c']],
            ['d', ['a', 'b', 'c']],
            [['e'], ['a', 'b', 'c']],
            [['a', 'e'], ['a', 'b', 'c']],
        ];
    }

    public static function provideValids()
    {
        return [
            ['a', ['a', 'b', 'c']],
            ['b', ['a', 'b', 'c']],
            [['a'], ['a', 'b', 'c']],
            [['a', 'b'], ['a', 'b', 'c']],
        ];
    }

    /**
     * @dataProvider provideInValids
     */
    public function testInValids(mixed $input, array $valids)
    {
        $validator = new InArray(['haystack' => $valids]);
        $result = $validator->isValid($input);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider provideValids
     */
    public function testValids(mixed $input, array $valids)
    {
        $validator = new InArray(['haystack' => $valids]);
        $result = $validator->isValid($input);
        $this->assertTrue($result);
    }
}