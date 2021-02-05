<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule;

use Exception;
use nicoSWD\Rule\Evaluator\EvaluatorInterface;

class Rule
{
    private string $rule;
    private Parser\Parser $parser;
    private string $parsedRule = '';
    private string $error = '';
    private static object $container;

    public function __construct(string $rule, array $variables = [])
    {
        if (!isset(self::$container)) {
            self::$container = require __DIR__ . '/container.php';
        }

        $this->parser = self::$container->parser($variables);
        $this->rule = $rule;
    }

    public function isTrue(): bool
    {
        /** @var EvaluatorInterface $evaluator */
        $evaluator = self::$container->evaluator();

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
