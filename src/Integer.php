<?php

namespace Zalt\Validator;

use function is_float;

use function is_int;
use function is_string;
use Laminas\Validator\AbstractValidator;
use Zalt\Filter\Integer as IntegerFilter;

/** @final */
class Integer extends AbstractValidator
{
    public const NOT_INTEGER  = 'notInteger';
    public const STRING_EMPTY = 'integerStringEmpty';
    public const INVALID      = 'integerInvalid';

    /**
     * Integer filter used for validation
     *
     * @var IntegerFilter|null
     */
    protected static $filter;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_INTEGER  => 'The input must contain only digits and an optional leading minus sign',
        self::STRING_EMPTY => 'The input is an empty string',
        self::INVALID      => 'Invalid type given. String, integer or float expected',
    ];

    /**
     * Returns true if and only if $value only contains digit characters and an optional leading minus sign
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue((string) $value);

        if ('' === $this->getValue()) {
            $this->error(self::STRING_EMPTY);
            return false;
        }

        if (null === static::$filter) {
            static::$filter = new IntegerFilter();
        }

        if ($this->getValue() !== static::$filter->filter($this->getValue())) {
            $this->error(self::NOT_INTEGER);
            return false;
        }

        return true;
    }
}
