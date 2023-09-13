<?php

declare(strict_types=1);

/**
 * @package    Zalt
 * @subpackage Validator
 * @author     Matijs de Jong <mjong@magnafacta.nl>
 */

namespace Zalt\Validator;

/**
 * @package    Zalt
 * @subpackage Validator
 * @since      Class available since version 1.0
 */
class Regexclude extends \Laminas\Validator\AbstractValidator
{
    const INVALID   = 'regexInvalid';
    const MATCH     = 'regexMatch';
    const ERROROUS  = 'regexErrorous';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID   => "Invalid type given. String, integer or float expected",
        self::MATCH     => "'%value%' does match against pattern '%pattern%'",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    ];

    /**
     * @var array
     */
    protected $messageVariables = [
        'pattern' => 'pattern'
    ];

    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected string $pattern;

    /**
     * Sets validator options
     *
     * @param  string $pattern regex
     * @return void
     */
    public function __construct(?string $pattern = null)
    {
        parent::__construct();
        if ($this->pattern && !$pattern) {
            return;
        }

        $this->setPattern($pattern);
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Sets the pattern option
     *
     * @param  string $pattern
     * @throws \RuntimeException if there is a fatal error in pattern matching
     */
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        $status        = @preg_match($this->pattern, "Test");

        if (false === $status) {
            throw new \RuntimeException(sprintf("Using the pattern '%s' generated the error: %s", $this->pattern, preg_last_error_msg()));
        }

        return $this;
    }

    /**
     * Defined by ValidatorInterface
     *
     * Returns true if and only if $value matches against the pattern option
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid(mixed $value): bool
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        $status = @preg_match($this->pattern, $value);
        if (false === $status) {
            $this->error(self::ERROROUS);
            return false;
        }

        if ($status) {
            $this->error(self::MATCH);
            return false;
        }

        return true;
    }
}