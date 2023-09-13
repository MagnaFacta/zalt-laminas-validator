<?php

declare(strict_types=1);


/**
 * @package    Zalt
 * @subpackage Validator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator;

use Zalt\Loader\ProjectOverloader;
use Zalt\Loader\ProjectOverloaderFactory;
use Zalt\Mock\SimpleServiceManager;
use Zalt\Model\MetaModelLoader;
use Zalt\Model\MetaModelLoaderFactory;
use Zalt\Model\Ra\PhpArrayModel;

/**
 * @package    Zalt
 * @subpackage Validator
 * @since      Class available since version 1.0
 */
trait ModelLoadedTrait
{
    public function getModelLoaded(array $rows): PhpArrayModel
    {
        $loader = $this->getModelLoader();

        $data  = new \ArrayObject($rows);
        /**
         * @var PhpArrayModel $model
         */
        $model = $loader->createModel(PhpArrayModel::class, 'test', $data);

        return $model;
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
}