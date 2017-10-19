<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Closure;
use InvalidArgumentException;
use nicoSWD\Rules\Core\CallableUserFunction;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Grammar\JavaScript\Functions\ParseFloat;
use nicoSWD\Rules\Grammar\JavaScript\Functions\ParseInt;
use nicoSWD\Rules\Tokens\BaseToken;

class Parser
{
    /** @var array */
    public $variables = [];

    /** @var null|mixed[] */
    private $values = null;

    /** @var null|BaseToken */
    private $operator =  null;

    /** @var TokenizerInterface */
    private $tokenizer;

    /** @var Expressions\Factory */
    private $expressionFactory;

    /** @var Callable[] */
    private $userDefinedFunctions = [];

    /** @var RuleGenerator */
    private $ruleGenerator;

    public function __construct(
        TokenizerInterface $tokenizer,
        Expressions\Factory $expressionFactory,
        RuleGenerator $ruleGenerator
    ) {
        $this->tokenizer = $tokenizer;
        $this->expressionFactory = $expressionFactory;
        $this->ruleGenerator = $ruleGenerator;

        $this->registerFunctionClass(ParseInt::class);
        $this->registerFunctionClass(ParseFloat::class);
    }

    public function parse(string $rule): string
    {
        $this->ruleGenerator->clear();
        $this->operator = null;
        $this->values = null;

        foreach (new AST($this->tokenizer->tokenize($rule), $this) as $token) {
            switch ($token->getType()) {
                case TokenType::VALUE:
                    $this->assignVariableValueFromToken($token);
                    break;
                case TokenType::LOGICAL:
                    $this->ruleGenerator->addLogical($token);
                    continue 2;
                case TokenType::PARENTHESES:
                    $this->ruleGenerator->addParentheses($token);
                    continue 2;
                case TokenType::OPERATOR:
                    $this->assignOperator($token);
                    continue 2;
                case TokenType::COMMENT:
                case TokenType::SPACE:
                    continue 2;
                default:
                    throw Exceptions\ParserException::unknownToken($token);
            }

            $this->parseExpression();
        }

        return $this->ruleGenerator->get();
    }

    public function assignVariables(array $variables)
    {
        $this->variables = $variables;
    }

    protected function assignVariableValueFromToken(BaseToken $token)
    {
        $this->ruleGenerator->flipOperatorRequired($token);

        if (!isset($this->values)) {
            $this->values = [$token->getValue()];
        } else {
            $this->values[] = $token->getValue();
        }
    }

    protected function assignOperator(BaseToken $token)
    {
        if (isset($this->operator)) {
            throw Exceptions\ParserException::unexpectedToken($token);
        } elseif (!isset($this->values)) {
            throw Exceptions\ParserException::incompleteExpression($token);
        }

        $this->ruleGenerator->operatorRequired(false);
        $this->operator = $token;
    }

    protected function parseExpression()
    {
        if (!isset($this->operator) || count($this->values) <> 2) {
            return;
        }

        $expression = $this->expressionFactory->createFromOperator($this->operator);
        $this->ruleGenerator->addBoolean($expression->evaluate(...$this->values));

        unset($this->operator, $this->values);
    }

    public function registerFunctionClass(string $className)
    {
        /** @var CallableUserFunction $function */
        $function = new $className();

        if (!$function instanceof CallableUserFunction) {
            throw new InvalidArgumentException(
                sprintf(
                    "%s must be an instance of %s",
                    $className,
                    CallableUserFunction::class
                )
            );
        }

        $this->registerFunction($function->getName(), function () use ($function): BaseToken {
            return $function->call(...func_get_args());
        });
    }

    public function registerToken(string $token, string $regex, int $priority = 10)
    {
        $this->tokenizer->registerToken($token, $regex, $priority);
    }

    public function getFunction(string $name): Closure
    {
        if (!isset($this->userDefinedFunctions[$name])) {
            throw new Exceptions\ParserException(sprintf(
                '%s is not defined',
                $name
            ));
        }

        return $this->userDefinedFunctions[$name];
    }

    private function registerFunction(string $name, Closure $callback)
    {
        $this->userDefinedFunctions[$name] = $callback;
    }
}
