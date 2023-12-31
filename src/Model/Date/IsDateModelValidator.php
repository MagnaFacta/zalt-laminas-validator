<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model\Date;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class IsDateModelValidator extends AbstractModelDateValidator
{
    /**
     * Error constants
     */
    public const NOT_DATE = 'not date';


    /**
     * Just to be able to use code completion, but also just in case you want to change the
     */
    public static string $notDateMessageKey = 'notDateMessage';

    /**
     * Validation failure message template definitions
     *
     * @var array<string, string>
     */
    protected array $messageTemplates = [
        self::NOT_DATE => "'%value%' is not a valid date in the format '%format%'.",
    ];

    protected array $messageVariables = [
        'format' => 'format',
        ];

    /**
     * @inheritDoc
     */
    public function isValid($value, $context = [])
    {
        $this->checkSetup();

        if (! $value) {
            // Do not test for any input
            return true;
        }

        $date = $this->getDateValue($value, $this->name);
        if ($date instanceof \DateTimeInterface) {
            return true;
        }

        // Error. Prepare message
        $this->checkValidatorMessage(self::$notDateMessageKey, self::NOT_DATE);
        $this->setFormatFromModel();
        $this->error(self::NOT_DATE);

        return false;
    }
}