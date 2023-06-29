<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

use PHPUnit\Framework\TestCase;
use Zalt\Loader\ProjectOverloader;
use Zalt\Loader\ProjectOverloaderFactory;
use Zalt\Mock\SimpleServiceManager;
use Zalt\Model\MetaModelLoader;
use Zalt\Model\MetaModelLoaderFactory;
use Zalt\Model\Ra\PhpArrayModel;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class ModelUniqueValidatorTest extends TestCase
{
    public function getModelLoaded(array $rows): PhpArrayModel
    {
        $loader = $this->getModelLoader();

        $data  = new \ArrayObject($rows);
        return $loader->createModel(PhpArrayModel::class, 'test', $data);
    }

    public function getModelLoader(): MetaModelLoader
    {
        static $loader;

        if ($loader instanceof MetaModelLoader) {
            return $loader;
        }

        $config = [
            'config' => [],
        ];
        $sm     = new SimpleServiceManager($config);
        $overFc = new ProjectOverloaderFactory();
        $sm->set(ProjectOverloader::class, $overFc($sm));

        $mmlf   = new MetaModelLoaderFactory();
        $loader = $mmlf($sm);

        return $loader;
    }

    public function getRows(): array
    {
        return [
            0 => ['a' => 'A1', 'b' => 'B1', 'c' => 20],
            1 => ['a' => 'A2', 'b' => 'B2', 'c' => 40],
            2 => ['a' => 'A3', 'b' => 'C3', 'c' => 10],
            3 => ['a' => 'A4', 'b' => 'D4', 'c' => 30],
        ];
    }

    public static function isValidProvider()
    {
        return [
            'changeValidOneKey1' => [['a'], 'b', ['a' => 'A2', 'b' => 'B3']],
            'changeValidTwoKeys1' => [['a', 'b'], 'c', ['a' => 'A2', 'b' => 'B2', 'c' => 35]],
        ];
    }

    /**
     * @dataProvider isValidProvider
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
}