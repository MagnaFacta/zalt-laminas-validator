<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

use Zalt\Model\Data\FullDataInterface;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class RequireOtherField extends AbstractBasicModelValidator
{
    /**
     * Error codes
     * @const string
     */
    public const REQUIRED  = 'required_validator';

    protected array $messageTemplates = [
        self::REQUIRED => "To set '%description%' you have to set '%fieldDescription%'.",
    ];

    /**
     * @var array
     */
    protected array $messageVariables = [
        'description' => 'description',
        'fieldDescription' => 'fieldDescription'
    ];


    protected string $description = '';

    /**
     * The field name against which to validate
     * @var string
     */
    protected string $fieldName = '';

    /**
     * Description of field name against which to validate
     * @var string
     */
    protected string $fieldDescription = '';

    /**
     * Just to be able to use code completion, but also just in case you want to change the
     */
    public static string $otherField = 'requiredOtherField';

    /**
     * @inheritDoc
     */
    public function isValid($value, $context = [])
    {
        $this->setValue((string) $value);

        if ($value) {
            $fieldSet = isset($context[$this->fieldName]) && $context[$this->fieldName];

            if (! $fieldSet)  {
                $this->error(self::REQUIRED);
                return false;
            }
        }
        return true;
    }

    public function setDataModel(FullDataInterface $model): void
    {
        parent::setDataModel($model);

        if ($this->name) {
            $metaModel = $model->getMetaModel();
            if (! $this->description) {
                $this->description = $metaModel->get($this->name, 'label');
            }

            $newField = $this->fieldName ? $this->fieldName : $metaModel->get($this->name, self::$otherField);
            if ($newField) {
                $this->fieldName = $newField;
                if (! $this->fieldDescription) {
                    $this->fieldDescription = $metaModel->get($newField, 'label');
                }
            }
        }
    }
}