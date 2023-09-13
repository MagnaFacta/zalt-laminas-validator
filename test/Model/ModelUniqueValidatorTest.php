<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

use PHPUnit\Framework\TestCase;
use Zalt\Validator\ModelLoadedTrait;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class ModelUniqueValidatorTest extends TestCase
{
    use ModelLoadedTrait;

    public function getRows(): array
    {
        return [
            0 => ['a' => 'A1', 'b' => 'B1', 'c' => 20],
            1 => ['a' => 'A2', 'b' => 'B2', 'c' => 40],
            2 => ['a' => 'A3', 'b' => 'C3', 'c' => 10],
            3 => ['a' => 'A4', 'b' => 'D4', 'c' => 30],
        ];
    }

    public function getRowsWith(): array
    {
        return [
            0 => ['a' => 'A1', 'b' => 'B2', 'c' => 20],
            1 => ['a' => 'A2', 'b' => 'B2', 'c' => 40],
            2 => ['a' => 'A3', 'b' => 'B3', 'c' => 10],
            3 => ['a' => 'A4', 'b' => 'B3', 'c' => 30],
        ];
    }

    public static function inValidProvider()
    {
        return [
            'changeInvalidOneKey1' => [['a'], 'b', ['a' => 'A2', 'b' => 'B1']],
            'changeInvalidOneKey2' => [['a'], 'b', ['a' => 'A3', 'b' => 'B1']],
            'changeInvalidTwoKeys1' => [['a', 'b'], 'c', ['a' => 'A2', 'b' => 'B2', 'c' => 30]],
        ];
    }

    public static function inValidWithProvider()
    {
        return [
            'changeInvalidWith1' => [['c', 'b'], ['a' => 'A1', 'b' => 'B2', 'c' => 40]],
            'changeInvalidWith2' => [['c', 'b'], ['a' => 'A2', 'b' => 'B2', 'c' => 20]],
            'changeInvalidWith3' => [['c', 'b'], ['a' => 'A1', 'b' => 'B3', 'c' => 10]],
            'changeInvalidWith4' => [[['name' => 'c', 'with' => 'b']], ['a' => 'A3', 'b' => 'B3', 'c' => 30]],
            'changeInvalidWith5' => [[['name' => 'c', 'with' => ['b']]], ['a' => 'A4', 'b' => 'B3', 'c' => 10]],
        ];
    }

    public static function isValidProvider()
    {
        return [
            'changeValidOneKey1' => [['a'], 'b', ['a' => 'A2', 'b' => 'B3']],
            'changeValidOneKey2' => [['a'], 'b', ['a' => 'A3', 'b' => 'B3']],
            'changeValidOneKey3' => [['a'], 'b', ['a' => 'A1', 'b' => 'B1']],
            'changeValidTwoKeys1' => [['a', 'b'], 'c', ['a' => 'A1', 'b' => 'B1', 'c' => 35]],
            'changeValidTwoKeys2' => [['a', 'b'], 'c', ['a' => 'A2', 'b' => 'B2', 'c' => 35]],
        ];
    }

    public static function isValidWithProvider()
    {
        return [
            'changeValidWith1' => [['c', 'b'], ['a' => 'A1', 'b' => 'B2', 'c' => 10]],
            'changeValidWith2' => [['c', 'b'], ['a' => 'A1', 'b' => 'B3', 'c' => 40]],
            'changeValidWith3' => [[['name' => 'c', 'with' => 'b']], ['a' => 'A3', 'b' => 'B2', 'c' => 10]],
            'changeValidWith4' => [[['name' => 'c', 'with' => ['b']]], ['a' => 'A4', 'b' => 'B2', 'c' => 60]],
        ];
    }

    /**
     * @dataProvider inValidProvider
     *
     * @param array $keys
     * @param string $field
     * @param array $context
     * @return void
     */
    public function testUniqueInvalid(array $keys, string $field, array $context)
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $model->getMetaModel()->setKeys($keys);

        $validator = new ModelUniqueValidator();
        $validator->setDataModel($model);
        $validator->setName($field);
        $this->assertFalse($validator->isValid($context[$field], $context));
    }

    /**
     * @dataProvider inValidWithProvider
     *
     * @param mixed $options
     * @param array $context
     * @return void
     */
    public function testUniqueInvalidWith(mixed $options, array $context)
    {
        $rows  = $this->getRowsWith();
        $model = $this->getModelLoaded($rows);
        $model->getMetaModel()->setKeys(['a']);

        $validator = new ModelUniqueValidator(...$options);
        $validator->setDataModel($model);
        $this->assertFalse($validator->isValid($context['c'], $context));
    }

    /**
     * @dataProvider isValidProvider
     *
     * @param array $keys
     * @param string $field
     * @param array $context
     * @return void
     */
    public function testUniqueValid(array $keys, string $field, array $context)
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $model->getMetaModel()->setKeys($keys);

        $validator = new ModelUniqueValidator($field);
        $validator->setDataModel($model);
        $this->assertTrue($validator->isValid($context[$field], $context));
    }

    /**
     * @dataProvider isValidWithProvider
     *
     * @param mixed $options
     * @param array $context
     * @return void
     */
    public function testUniqueValidWith(mixed $options, array $context)
    {
        $rows  = $this->getRowsWith();
        $model = $this->getModelLoaded($rows);
        $model->getMetaModel()->setKeys(['a']);

        $validator = new ModelUniqueValidator(...$options);
        $validator->setDataModel($model);
        $this->assertTrue($validator->isValid($context['c'], $context));
    }
}