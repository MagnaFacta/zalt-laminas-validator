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
class RegexcludeTest extends \PHPUnit\Framework\TestCase
{
    public static function provideValues()
    {
        return [
            'yes' => ['/abc/', 'xuzabcgrs', false],
            'nope' => ['/^abc$/', 'xuzabcgrs', true],
            'object' => ['/^abc$/', ['xuzabcgrs'], false],
        ];
    }

    /**
     * @dataProvider provideValues
     * @param $input
     * @param $output
     * @return void
     */
    public function testValidator(string $regex, mixed $input, bool $output)
    {
        $validator = new Regexclude($regex);
        $result = $validator->isValid($input);
        $this->assertEquals($output, $result);
        $this->assertEquals($regex, $validator->getPattern());
    }

    public function testError()
    {
        $this->expectException(\RuntimeException::class);
        $validator = new Regexclude('/abc');
    }

    public function testNull()
    {
        $this->expectException(\TypeError::class);
        $validator = new Regexclude();
    }
}