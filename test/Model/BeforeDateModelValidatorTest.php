<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

use Zalt\Model\MetaModelInterface;
use Zalt\Validator\ModelLoadedTrait;

/**
 * @package    Zalt
 * @subpackage Validator\Model
 * @since      Class available since version 1.0
 */
class BeforeDateModelValidatorTest extends \PHPUnit\Framework\TestCase
{
    use ModelLoadedTrait;

    public function getRows(): array
    {
        return [
            0 => ['a' => 'A1', 'datefield' => '2022-02-12', 'beforeDateField' => '2024-02-12'],
            1 => ['a' => 'A2', 'datefield' => '2022-02-12', 'beforeDateField' => '1999-02-12'],
            2 => ['a' => 'A2', 'datefield' => '2022-02-12', 'beforeDateField' => null],
        ];
    }

    public static function invalidDateProvider(): array
    {
        return [
            'invalid date' => [0, '1002-02-2022', 'beforeDateField', BeforeDateModelValidator::notDateMessage, null, "'1002-02-2022' is not a valid date in the format 'dd-mm-yyyy'."],
            'no previous date' => [2, '20-12-2022', 'beforeDateField', BeforeDateModelValidator::noPreviousDateMessage, null, "Date should be empty if no valid before date is set."],
            'not before early' => [1, '20-12-2022', 'beforeDateField', BeforeDateModelValidator::beforeDateMessage, null, "The maximum date should be '12-02-1999' or earlier."],
            'incorrect format' => [0, '10-20-20211', 'beforeDateField', BeforeDateModelValidator::notDateMessage, "bla die bla", "bla die bla"],
            'not before message' => [1, '20-12-2022', 'beforeDateField', BeforeDateModelValidator::beforeDateMessage, "bla '%beforeValue%' or bla.", "bla '12-02-1999' or bla."],
        ];
    }

    /**
     * @dataProvider invalidDateProvider
     *
     * @param $dateInput
     * @param string|null $newMessage
     * @param string $resultMessage
     * @return void
     */
    public function testBeforeDateInvalid($row, $dateInput, $dateBefore, ?string $messageKey, ?string $newMessage, string $resultMessage): void
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();
        $metaModel->set('datefield', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
            BeforeDateModelValidator::beforeDateField => 'beforeDateField',
        ]);
        if ($messageKey) {
            $metaModel->set('datefield', [$messageKey => $newMessage]);
        }
        $metaModel->set('beforeDateField', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
        ]);

        $context = $rows[$row];
        $context['datefield'] = $dateInput;

        $validator = new BeforeDateModelValidator();
        $validator->setName('datefield');
        $validator->setDataModel($model);
        $this->assertFalse($validator->isValid($dateInput, $context));

        $message = implode('', $validator->getMessages());
        // echo "\n" . $message . "\n";

        $this->assertEquals($resultMessage, $message);
    }

    /**
     * @dataProvider validDateProvider
     * @param $dateInput
     * @return void
     */
    public function testBeforeDateValid($row, $dateInput, $dateBefore): void
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();
        $metaModel->set('datefield', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
            BeforeDateModelValidator::beforeDateField => 'beforeDateField',
            ]);
        $metaModel->set('beforeDateField', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
        ]);

        $context = $rows[$row];
        $context['datefield'] = $dateInput;

        $validator = new BeforeDateModelValidator();
        $validator->setName('datefield');
        $validator->setDataModel($model);
        $this->assertTrue($validator->isValid($dateInput, $context));

    }

    public static function validDateProvider(): array
    {
        return [
            'use db field' => [0, '02-02-2022', 'beforeDateField'],
            'use model setting' => [0, '02-02-2022 23:45', null],
            'no date' => [0, null, '2023-02-01'],
            'no date and model setting' => [0, '', null],
            'just two dates' => [0, new \DateTime(), new \DateTime('next year')],
            'early date' => [1, '12-10-1800', 'beforeDateField'],
            'no dates' => [2, null, 'beforeDateField'],
        ];
    }

}