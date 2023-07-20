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
class BeforeDateModelValidator extends AbstractModelDateValidator
{
    /**
     * Just to be able to use code completion, so I decided not to use all caps
     */
    public const beforeDateMessage = 'beforeDateMessage';
    public const beforeDateField = 'beforeDateField';
    public const noPreviousDateMessage = 'noPreviousDateMessage';
    public const notDateMessage = 'notDateMessage';

    public const NOT_BEFORE = 'notBefore';

    public const NOT_DATE = 'notDate';
    public const NO_VALIDFROM = 'noValidFrom';

    protected $beforeDate;

    protected $beforeValue;

    /**
     * Validation failure message template definitions
     *
     * @var array<string, string>
     */
    protected array $messageTemplates = [
        self::NOT_BEFORE => "The maximum date should be '%beforeValue%' or earlier.",
        self::NOT_DATE => "'%value%' is not a valid date in the format '%format%'.",
        self::NO_VALIDFROM => "Date should be empty if no valid before date is set."
    ];

    protected array $messageVariables = [
        'beforeValue' => 'beforeValue',
        'format' => 'format',
        ];

    /**
     * @param $beforeDate
     * /
    public function __construct($beforeDate = null)
    {
        $this->beforeDate = $beforeDate;
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

        if (null === $this->beforeDate) {
            // Check model for setting
            $this->beforeDate = $this->model->getMetaModel()->get($this->name, self::beforeDateField);
//            echo 'Before date: ' . $this->beforeDate . "\n";
        }
        if (null === $this->beforeDate) {
            $before = new \DateTimeImmutable();
        } elseif ($this->beforeDate instanceof \DateTimeInterface) {
            $before = $this->beforeDate;
        } elseif (array_key_exists($this->beforeDate, $context)) {
            $before = $this->getDateValue($context[$this->beforeDate], $this->beforeDate);
        } else {
            $before = false;
        }

        if (! $before instanceof \DateTimeInterface) {
            $this->checkValidatorMessage(self::noPreviousDateMessage, self::NO_VALIDFROM);
            $this->error(self::NO_VALIDFROM);
            return false;
        }
//        echo 'Before: ' . $this->formatDate($before) . "\n";

        if (! $date instanceof \DateTimeInterface) {
            $this->setValue($value);
            $this->checkValidatorMessage(self::notDateMessage, self::NOT_DATE);
            $this->error(self::NOT_DATE);
            return false;
        }
//        echo 'Before: ' . $this->formatDate($before) . ' >= ' . $this->formatDate($before) . "\n";

        if ($date->getTimestamp() >= $before->getTimestamp()) {
            $this->checkValidatorMessage(self::beforeDateMessage, self::NOT_BEFORE);
            $this->setFormatFromModel();
            $this->beforeValue = $this->formatDate($before);
            $this->error(self::NOT_BEFORE);
//            dump($this->getMessages());
            return false;
        }

        return true;
    }
}