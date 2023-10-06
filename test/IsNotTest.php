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
class IsNotTest extends \PHPUnit\Framework\TestCase
{
    public static function provideFilters()
    {
        return [
            'ok' => [[1, 2, 3], 4, true],
            'not' => [[1, 2, 3], 3, false],
        ];
    }

    /**
     * @dataProvider provideFilters
     * @param $input
     * @param $output
     * @return void
     */
    public function testValidator(array $not, mixed $input, bool $output)
    {
        $validator = new IsNot($not);
        $result = $validator->isValid($input);
        $this->assertEquals($output, $result);
    }
}