<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule;

use Exception;
use nicoSWD\Rule\AST\AstEvaluator;
use nicoSWD\Rule\AST\Node;

class Rule
{
    private readonly Parser\Parser $parser;
    private readonly AstEvaluator $astEvaluator;
    private string $rule;
    private ?Node $ast = null;
    private string $error = '';
    private static object $container;

    public function __construct(string $rule, array $variables = [])
    {
        if (!isset(self::$container)) {
            self::$container = require __DIR__ . '/container.php';
        }

        $this->parser = self::$container->parser($variables);
        $this->astEvaluator = self::$container->astEvaluator($variables);
        $this->rule = $rule;
    }

    /** @throws Parser\Exception\ParserException */
    public function isTrue(): bool
    {
        if ($this->ast === null) {
            $this->ast = $this->parser->parse($this->rule);
        }

        return $this->astEvaluator->evaluate($this->ast);
    }

    /** @throws Parser\Exception\ParserException */
    public function isFalse(): bool
    {
        return !$this->isTrue();
    }

    /**
     * Tells whether a rule is valid (as in "can be parsed and evaluated without error") or not.
     */
    public function isValid(): bool
    {
        try {
            $this->ast = $this->parser->parse($this->rule);
            $this->astEvaluator->evaluate($this->ast);
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
