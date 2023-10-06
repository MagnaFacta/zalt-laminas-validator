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
class IsNot extends \Laminas\Validator\AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    public const NOT_ONE = 'notOne';

    protected array $messageTemplates = [
        self::NOT_ONE => "This value is not allowed.",
    ];

    /**
     * The field name against which to validate
     *
     * @var mixed
     */
    protected mixed $notAllowedValues;

    /**
     * Sets validator options
     *
     * @param array $values On or more values that this element should not have
     * @param string $message Optional different message
     */
    public function __construct(array $values, ?string $message = null)
    {
        parent::__construct();
        $this->notAllowedValues = $values;

        if ($message) {
            $this->setMessage($message, self::NOT_ONE);
        }
    }

    public function isValid(mixed $value, array $context = [])
    {
        if (in_array($value, $this->notAllowedValues)) {
            $this->setValue((string) $value);
            $this->error(self::NOT_ONE);
            return false;
        }

        return true;
    }
}