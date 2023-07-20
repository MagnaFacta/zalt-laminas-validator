<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

use Laminas\Validator\Exception\InvalidArgumentException;
use Zalt\Model\MetaModelInterface;
use Zalt\Ra\Ra;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class ModelUniqueValidator extends AbstractBasicModelValidator
{
    public const FOUND = 'found';

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

    public function __construct($name = null, ...$with)
    {
        if (is_array($name)) {
            $options = $name;
        } elseif ($name instanceof \Traversable) {
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
        $this->checkSetup();

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
}