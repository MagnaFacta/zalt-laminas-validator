<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

use Laminas\Validator\Exception\InvalidArgumentException;
use Zalt\Model\Data\FullDataInterface;
use Zalt\Model\Validator\ModelAwareValidatorInterface;
use Zalt\Model\Validator\NameAwareValidatorInterface;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
abstract class AbstractBasicModelValidator extends \Laminas\Validator\AbstractValidator
    implements ModelAwareValidatorInterface, NameAwareValidatorInterface
{
    protected string $name;

    protected FullDataInterface $model;

    protected function checkValidatorMessage(string $modelKey, string $messageKey)
    {
        $message = $this->model->getMetaModel()->get($this->name, $modelKey);
        if ($message) {
            $this->setMessage($message, $messageKey);
        }
    }

    protected function checkSetup(): void
    {
        if (! isset($this->model)) {
            throw new InvalidArgumentException(sprintf("No model set for class %s.", get_class($this)));
        }
        if (! isset($this->name)) {
            throw new InvalidArgumentException(sprintf("No name set for class %s.", get_class($this)));
        }
    }

    public function setDataModel(FullDataInterface $model): void
    {
        $this->model = $model;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}