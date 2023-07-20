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
class AfterDateModelValidatorTest extends \PHPUnit\Framework\TestCase
{
    use ModelLoadedTrait;

    public function getRows(): array
    {
        return [
            0 => ['a' => 'A1', 'datefield' => '2022-02-12', 'afterDateField' => '1999-02-12'],
            1 => ['a' => 'A2', 'datefield' => '2022-02-12', 'afterDateField' => '2024-02-12'],
            2 => ['a' => 'A2', 'datefield' => '2022-02-12', 'afterDateField' => null],
        ];
    }

    public static function invalidDateProvider(): array
    {
        return [
            'invalid date' => [0, '1002-02-2022', 'afterDateField', AfterDateModelValidator::notDateMessage, null, "'1002-02-2022' is not a valid date in the format 'dd-mm-yyyy'."],
            'no previous date' => [2, '20-12-2022', 'afterDateField', AfterDateModelValidator::noPreviousDateMessage, null, "Date should be empty if no valid after date is set."],
            'not after early' => [1, '20-12-2022', 'afterDateField', AfterDateModelValidator::afterDateMessage, null, "The minimum date should be '12-02-2024' or later."],
            'incorrect format' => [0, '10-20-20211', 'afterDateField', AfterDateModelValidator::notDateMessage, "bla die bla", "bla die bla"],
            'not after message' => [1, '20-12-2022', 'afterDateField', AfterDateModelValidator::afterDateMessage, "bla '%afterValue%' or bla.", "bla '12-02-2024' or bla."],
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
    public function testAfterDateInvalid($row, $dateInput, $dateAfter, ?string $messageKey, ?string $newMessage, string $resultMessage): void
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();
        $metaModel->set('datefield', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
            AfterDateModelValidator::afterDateField => 'afterDateField',
        ]);
        if ($messageKey) {
            $metaModel->set('datefield', [$messageKey => $newMessage]);
        }
        $metaModel->set('afterDateField', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
        ]);

        $context = $rows[$row];
        $context['datefield'] = $dateInput;

        $validator = new AfterDateModelValidator();
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
    public function testAfterDateValid($row, $dateInput, $dateAfter): void
    {
        $rows  = $this->getRows();
        $model = $this->getModelLoaded($rows);
        $metaModel = $model->getMetaModel();
        $metaModel->set('datefield', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
            AfterDateModelValidator::afterDateField => 'afterDateField',
            ]);
        $metaModel->set('afterDateField', [
            MetaModelInterface::TYPE_ID => MetaModelInterface::TYPE_DATE,
        ]);

        $context = $rows[$row];
        $context['datefield'] = $dateInput;

        $validator = new AfterDateModelValidator();
        $validator->setName('datefield');
        $validator->setDataModel($model);
        $this->assertTrue($validator->isValid($dateInput, $context));

    }

    public static function validDateProvider(): array
    {
        return [
            'use db field' => [0, '02-02-2022', 'afterDateField'],
            'use model setting' => [0, '02-02-2022 23:45', null],
            'no date' => [0, null, '2023-02-01'],
            'no date and model setting' => [0, '', null],
            'just two dates' => [0, new \DateTime(), new \DateTime('next year')],
            'late date' => [1, '12-10-2400', 'afterDateField'],
            'no dates' => [2, null, 'afterDateField'],
        ];
    }

}