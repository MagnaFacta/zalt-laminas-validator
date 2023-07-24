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
use Zalt\Validator\ModelLoadedTrait;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class IsDateModelValidatorTest extends \PHPUnit\Framework\TestCase
{
    use ModelLoadedTrait;

    public function getRows(): array
    {
        return [
            0 => ['a' => 'A1', 'datefield' => '2022-02-12'],
        ];
    }

    public static function invalidDateProvider(): array
    {
        return [
            ['1002-02-2022', null, "'1002-02-2022' is not a valid date in the format 'dd-mm-yyyy'."],
            ['020222022', "bla '%value%' die bla", "bla '020222022' die bla"],
            ['xx-xx-xxxx', "bla die '%format%' bla", "bla die 'dd-mm-yyyy' bla"],
            ['10-20-20211', "bla die bla", "bla die bla"],
            ['10-22-2021', "bla '%value%' die '%format%' bla", "bla '10-22-2021' die 'dd-mm-yyyy' bla"],
        ];
    }

    public function testSetupCheck1()
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();

        $validator = new IsDateModelValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->isValid('20-12-2012');
    }

    public function testSetupCheck2()
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();

        $validator = new IsDateModelValidator();
        $validator->setDataModel($model);
        $this->expectException(InvalidArgumentException::class);
        $validator->isValid('20-12-2012');
    }

    public function testSetupCheck3()
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();
        $metaModel->set('datefield', [MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE]);

        $validator = new IsDateModelValidator();
        $validator->setDataModel($model);
        $validator->setName('datefield');
        $this->assertTrue($validator->isValid('20-12-2012'));
    }

    /**
     * @dataProvider invalidDateProvider
     *
     * @param $dateInput
     * @param string|null $newMessage
     * @param string $resultMessage
     * @return void
     */
    public function testIsDateInValid($dateInput, ?string $newMessage, string $resultMessage): void
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();
        $metaModel->set('datefield', [MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE]);
        if ($newMessage) {
            $metaModel->set('datefield', IsDateModelValidator::$notDateMessageKey, $resultMessage);
        }

        $validator = new IsDateModelValidator();
        $validator->setName('datefield');
        $validator->setDataModel($model);
        $this->assertFalse($validator->isValid($dateInput));

        $message = implode('', $validator->getMessages());
        // echo "\n" . $message . "\n";

        $this->assertEquals($resultMessage, $message);
    }

    /**
     * @dataProvider validDateProvider
     * @param $dateInput
     * @return void
     */
    public function testIsDateValid($dateInput): void
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $model->getMetaModel()->set('datefield', [MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE]);

        $validator = new IsDateModelValidator();
        $validator->setName('datefield');
        $validator->setDataModel($model);
        $this->assertTrue($validator->isValid($dateInput));

    }

    public static function validDateProvider(): array
    {
        return [
            ['02-02-2022'],
            ['02-02-2202 23:45'],
            [null],
            [''],
            [new \DateTime()]
        ];
    }
}