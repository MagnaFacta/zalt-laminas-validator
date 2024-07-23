<?php
                
/**
 *
 * @package    Zalt
 * @subpackage Validator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator\Model;

/**
 *
 * @package    Zalt
 * @subpackage Validator
 * @since      Class available since version 1.0
 */
class CheckedItemsRangeValidator extends AbstractBasicModelValidator
{
    /**
     * Error codes
     */
    public const TOO_LESS = 'tooLess';
    public const TOO_MUCH = 'tooMuch';

    /**
     * Templates for different error message types
     */
    protected array $_messageTemplates = [
        self::TOO_LESS => "At least %min% checked value(s) required",
        self::TOO_MUCH => "Not more then %max% checked value(s) allowed",
    ];

    protected array $_messageVariables = [
        'min' => '_valMin',
        'max' => '_valMax'
    ];

    protected array $_ranOnce  = [];
    protected $_valField = null;
    protected $_valMax   = null;
    protected $_valMin   = null;

    /**
     * Constructor for the integer validator
     *
     * @param string $field The field name
     * @param int $min Min value to compare items number with
     * @param int $max Max value to compare items number with
     */
    public function __construct($field, $min, $max)
    {
        $this->_valField = $field;
        $this->_valMin = $min;
        $this->_valMax = $max;
    }

    /**
     * Defined by ValidatorInterface
     *
     * Returns true if and only if the provided field contains a number of items in
     * the range [min, max].
     *
     * @param string|integer $value
     * @param array $context
     * @return boolean
     */
    public function isValid($value, $context = [])
    {
        if (isset($this->_ranOnce[$this->_valField]) || ! isset($context[$this->_valField])) {
            return true;
        }

        $this->_ranOnce[$this->_valField] = true;
        if ($this->_valMin > 0 && count($context[$this->_valField]) < $this->_valMin) {
            $this->error(self::TOO_LESS);
            return false;
        }

        // Value should be less
        if ($this->_valMax > 0 && count($context[$this->_valField]) > $this->_valMax) {
            $this->error(self::TOO_MUCH);
            return false;
        }
        return true;
    }
}