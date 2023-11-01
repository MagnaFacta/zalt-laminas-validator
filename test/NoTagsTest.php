<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator;

/**
 * @package    Zalt
 * @subpackage Validator
 * @since      Class available since version 1.0
 */
class NoTagsTest extends \PHPUnit\Framework\TestCase
{
    public static function provideValues()
    {
        return [
            'yes' => ['xuzabcgrs', true],
            'savespace' => ['xu< zabcgrs', true],
            'saveamp' => ['xu& zabcgrs', true],
            'tagged' => ['xu<z/>abcgrs', false],
            'amped' => ['xu&amp;abcgrs', false],
            'tag colon' => ['xu<:z/>abcgrs', false],
            'amp colon' => ['xu&:amp;abcgrs', true], // Not in description but allowed
            'tag slash' => ['xu<z/>abcgrs', false],
            'amp slash' => ['xu&\amp;abcgrs', true], // Not in description but allowed
            'strange' => ['<<&<<', true],
            'yes array' => [['abc', 'def'], true],
            'no array' => [['a<b>c', 'd&e;f'], false],
            'some array' => [['abc', 'd<e/>f'], false],
            'null' => [null, true],
            'int' => [42, true],
            'empty' => ['', true],
            'empty array' => [[], true],
            'object' => [new \ArrayObject(), true],
        ];
    }

    /**
     * @dataProvider provideValues
     * @param $input
     * @param $output
     * @return void
     */
    public function testValidator(mixed $input, bool $output)
    {
        $validator = new NoTags();
        $result = $validator->isValid($input);
        $this->assertEquals($output, $result);
    }
}