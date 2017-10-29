<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Exception;
use nicoSWD\Rules\Evaluator\EvaluatorInterface;

class Rule
{
    /** @var string */
    private $rule;
    /** @var Parser\Parser */
    private $parser;
    /** @var string */
    private $parsedRule = '';
    /** @var string */
    private $error = '';
    /** @var object */
    private $container;

    public function __construct(string $rule, array $variables = [])
    {
        $this->container = require __DIR__ . '/container.php';
        $this->parser = $this->container->parser($variables);
        $this->rule = $rule;
    }

    public function isTrue(): bool
    {
        /** @var EvaluatorInterface $evaluator */
        $evaluator = $this->container->evaluator();

        return $evaluator->evaluate(
            $this->parsedRule ?:
            $this->parser->parse($this->rule)
        );
    }

    public function isFalse(): bool
    {
        return !$this->isTrue();
    }

    /**
     * Tells whether a rule is valid (as in "can be parsed without error") or not.
     */
    public function isValid(): bool
    {
        try {
            $this->parsedRule = $this->parser->parse($this->rule);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return true;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
