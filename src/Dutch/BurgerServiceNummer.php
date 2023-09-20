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
class BurgerServiceNummer extends \Zalt\Validator\ElevenTest
{
    protected array|int $numberOrder = [9, 8, 7, 6, 5, 4, 3, 2, -1];

    /**
     * Description of the kind of test
     *
     * @var string
     */
    protected string $testDescription = 'burgerservicenummer';

    /**
     * Defined by ValidatorInterface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value): bool
    {
        if ($value && trim((string) $value, '*')) {
            return parent::isValid((string) $value);
        }
        return true;
    }
}