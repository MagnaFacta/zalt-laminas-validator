<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator;

use Zalt\Validator\Model\CheckedItemsRangeValidator;

/**
 * @package    Zalt
 * @subpackage Validator
 * @since      Class available since version 1.0
 */
class CheckedItemsRangeTest extends \PHPUnit\Framework\TestCase
{
    public static function provideValues()
    {
        // field, count, min, max, output
        return [
            ['test', 1, 1, 1, true],
            ['test', 0, 1, 1, false],
            ['test', 1, 0, 1, true],
            ['test', 1, 1, 0, true],
            ['test', 0, 0, 0, true],
            ['test', 0, 1, 0, false],
            ['test', 0, 0, 1, true],
            ['test', 99, 1, 100, true],
            ['test', 101, 1, 100, false],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidator(string $field, int $count, int $min, int $max, bool $output): void
    {
        $validator = new CheckedItemsRangeValidator($field, $min, $max);
        $context[$field] = [];
        for ($i=0; $i < $count; $i++) {
            $context[$field][] = $i;
        }
        $result = $validator->isValid('', $context);
        $this->assertEquals($output, $result);
    }
}