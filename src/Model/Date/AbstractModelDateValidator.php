<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model\Date;

use Laminas\Validator\Exception\InvalidArgumentException;
use Zalt\Model\MetaModel;
use Zalt\Validator\Model\AbstractBasicModelValidator;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
abstract class AbstractModelDateValidator extends AbstractBasicModelValidator
{
    protected string $format;

    protected function formatDate(\DateTimeInterface $date): string
    {
        return $date->format($this->model->getMetaModel()->get($this->name, 'dateFormat'));
    }

    protected function getDateValue(mixed $value, string $name)
    {
        $metaModel = $this->model->getMetaModel();

        if ($value instanceof \DateTimeInterface) {
            $this->setValue($this->formatDate($value));
        } else {
            $this->setValue($value);
        }

        $transformer = $metaModel->get($name, MetaModel::LOAD_TRANSFORMER);
        if (! is_callable($transformer)) {
            throw new InvalidArgumentException(sprintf("No load transformer set field %s.", $name));
        }
        return $transformer($value, false, $name, [], false);
    }

    protected function setFormatFromModel()
    {
        $format = null;
        $metaModel = $this->model->getMetaModel();
        foreach (['description', 'dateFormat'] as $key) {
            $format = $metaModel->get($this->name, $key);
            if ($format) {
                break;
            }
        }
        $this->format = $format ?? 'dd-mm-yyyy';
    }
}
