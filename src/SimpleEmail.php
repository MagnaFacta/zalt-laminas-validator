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
class SimpleEmail extends \Laminas\Validator\AbstractValidator
{
    public const INVALID   = 'emailInvalid';
    public const NOT_MATCH = 'emailNotMatch';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID   => "Invalid type given, value should be string, integer or float",
        self::NOT_MATCH => "'%value%' is not an email address (e.g. name@somewhere.com).",
    ];

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        if ($value === '') {
            return true;
        }
        $this->setValue($value);

        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $status = filter_var($value, FILTER_VALIDATE_EMAIL);
        if (false === $status) {
            $this->error(self::NOT_MATCH);
            return false;
        }

        return true;
    }
}