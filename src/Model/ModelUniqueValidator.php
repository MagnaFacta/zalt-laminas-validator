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
use Zalt\Model\MetaModelInterface;
use Zalt\Model\Validator\ModelAwareValidatorInterface;
use Zalt\Ra\Ra;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class ModelUniqueValidator extends \Laminas\Validator\AbstractValidator implements ModelAwareValidatorInterface
{
    public const FOUND = 'found';

    protected array $combo;

    protected string $field;

    /**
     * Validation failure message template definitions
     *
     * @var array<string, string>
     */
    protected $messageTemplates = [
        self::FOUND => "A duplicate '%value%' item was found.",
    ];

    protected FullDataInterface $model;

    public function __construct($field = null, ...$combo)
    {
        if (is_array($field)) {
            $options = $field;
        } elseif ($field instanceof Traversable) {
            $options = Ra::to($field);
        } else {
            $options['field'] = $field;
        }

        if ($combo && (! isset($options['combo']))) {
            $options['combo'] = $combo;
        }

        parent::__construct($options);
    }

    /**
     * @inheritDoc
     */
    public function isValid($value, $context = [])
    {
        if (! isset($this->model)) {
            throw new InvalidArgumentException(sprintf("No model set for class %s.", get_class($this)));
        }
        if (! isset($this->field)) {
            throw new InvalidArgumentException(sprintf("No field set for class %s.", get_class($this)));
        }

        $filter[$this->field] = $context[$this->field] ?? $value;
        $this->setValue($filter[$this->field]);
        if (isset($this->combo)) {
            foreach ($this->combo as $name) {
                if (isset($context[$name])) {
                    $filter[$name] = $context[$name];
                }
            }
        }
        $keys = $this->model->getMetaModel()->getKeys();
        foreach ($keys as $name) {
            if (isset($context[$name]) && $context[$name]) {
                $filter[MetaModelInterface::FILTER_NOT][$name] = $context[$name];
            }
        }

        // dump($filter);
        if ($this->model->loadCount($filter)) {
            $this->error(self::FOUND);
            return false;
        }

        return true;
    }

    public function setCombo($combo)
    {
        $this->combo = (array) $combo;
    }

    public function setDataModel(FullDataInterface $model): void
    {
        $this->model = $model;
    }

    public function setField(string $field)
    {
        $this->field = $field;
    }
}