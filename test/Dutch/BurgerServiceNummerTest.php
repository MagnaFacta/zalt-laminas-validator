<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Dutch
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Dutch;

/**
 * @package    Zalt
 * @subpackage Validator\Dutch
 * @since      Class available since version 1.0
 */
class BurgerServiceNummerTest extends \PHPUnit\Framework\TestCase
{
    public static function provideFilters()
    {
        return [
            'normal' => ['123456789', false],
            'rvm'    => ['000000012', true],
            'rvmNot' => ['000000022', false],
            'random' => ['267354514', true],
            'false'  => ['223456789', false],
            'stars'  => ['*********', true],
            'null'   => [null, true],
            'empty'   => ['', true],
        ];
    }

    /**
     * @dataProvider provideFilters
     * @param $input
     * @param $output
     * @return void
     */
    public function testValidator($input, $output)
    {
        $validator = new BurgerServiceNummer();
        $result = $validator->isValid($input);
        $this->assertEquals($output, $result);
    }

}