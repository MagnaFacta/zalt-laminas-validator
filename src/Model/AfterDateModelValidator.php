<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class AfterDateModelValidator extends AbstractModelDateValidator
{
    /**
     * Error constants
     */
    public const NOT_AFTER = 'notAfter';
    public const NOT_DATE = 'notDate';
    public const NO_VALIDFROM = 'noValidFrom';

    /**
     * Just to be able to use code completion, but also just in case you want to change the
     */
    public static string $afterDateMessageKey = 'afterDateMessage';
    public static string $afterDateFieldKey = 'afterDateField';
    public static string $noPreviousDateMessageKey = 'noPreviousDateMessage';
    public static string $notDateMessageKey = 'notDateMessage';

    protected $afterDate;

    protected $afterValue;

    /**
     * Validation failure message template definitions
     *
     * @var array<string, string>
     */
    protected array $messageTemplates = [
        self::NOT_AFTER => "The minimum date should be '%afterValue%' or later.",
        self::NOT_DATE => "'%value%' is not a valid date in the format '%format%'.",
        self::NO_VALIDFROM => "Date should be empty if no valid after date is set."
    ];

    protected array $messageVariables = [
        'afterValue' => 'afterValue',
        'format' => 'format',
    ];

    /**
     * @param $afterDate
     * /
    public function __construct($afterDate = null)
    {
    $this->afterDate = $afterDate;
    }

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
        $this->setFormatFromModel();

        if (null === $this->afterDate) {
            // Check model for setting
            $this->afterDate = $this->model->getMetaModel()->get($this->name, self::$afterDateFieldKey);
//            echo 'After date: ' . $this->afterDate . "\n";
        }
        if (null === $this->afterDate) {
            $after = new \DateTimeImmutable();
        } elseif ($this->afterDate instanceof \DateTimeInterface) {
            $after = $this->afterDate;
        } elseif (array_key_exists($this->afterDate, $context)) {
            $after = $this->getDateValue($context[$this->afterDate], $this->afterDate);
        } else {
            $after = false;
        }

        if (! $after instanceof \DateTimeInterface) {
            $this->checkValidatorMessage(self::$noPreviousDateMessageKey, self::NO_VALIDFROM);
            $this->error(self::NO_VALIDFROM);
            return false;
        }
//        echo 'After: ' . $this->formatDate($after) . "\n";

        if (! $date instanceof \DateTimeInterface) {
            $this->setValue($value);
            $this->checkValidatorMessage(self::$notDateMessageKey, self::NOT_DATE);
            $this->error(self::NOT_DATE);
            return false;
        }
//        echo 'After: ' . $this->formatDate($after) . ' <= ' . $this->formatDate($after) . "\n";

        if ($date->getTimestamp() <= $after->getTimestamp()) {
            $this->checkValidatorMessage(self::$afterDateMessageKey, self::NOT_AFTER);
            $this->setFormatFromModel();
            $this->afterValue = $this->formatDate($after);
            $this->error(self::NOT_AFTER);
//            dump($this->getMessages());
            return false;
        }

        return true;
    }
}
