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
use Zalt\Model\Validator\NameAwareValidatorInterface;
use Zalt\Ra\Ra;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class ModelUniqueValidator extends \Laminas\Validator\AbstractValidator
    implements ModelAwareValidatorInterface, NameAwareValidatorInterface
{
    public const FOUND = 'found';

    protected string $name;

    /**
     * @var array Extra fields to determine uniqueness
     */
    protected array $with = [];

    /**
     * Validation failure message template definitions
     *
     * @var array<string, string>
     */
    protected $messageTemplates = [
        self::FOUND => "A duplicate '%value%' item was found.",
    ];

    protected FullDataInterface $model;

    public function __construct($name = null, ...$with)
    {
        if (is_array($name)) {
            $options = $name;
        } elseif ($name instanceof Traversable) {
            $options = Ra::to($name);
        } elseif ($name) {
            $options['name'] = $name;
        } else {
            $options = [];
        }

        if ($with && (! isset($options['with']))) {
            $options['with'] = $with;
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
        if (! isset($this->name)) {
            throw new InvalidArgumentException(sprintf("No name set for class %s.", get_class($this)));
        }

        $filter[$this->name] = $context[$this->name] ?? $value;
        $this->setValue($filter[$this->name]);
        if ($this->with) {
            foreach ($this->with as $name) {
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

    public function setWith($with)
    {
        if (is_array($with)) {
            $this->with = $with;
        } elseif (is_scalar($with)) {
            $this->with = (array) $with;
        } else {
            $this->with = Ra::to($with);
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