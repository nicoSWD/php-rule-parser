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
use nicoSWD\Rules\Tokens\BaseToken;
use SplStack;

class Parser
{
    /** @var array */
    public $variables = [];

    /** @var null|BaseToken */
    private $operator = null;

    /** @var SplStack */
    private $values;

    /** @var TokenizerInterface */
    private $tokenizer;

    /** @var Expressions\ExpressionFactory */
    private $expressionFactory;

    /** @var Callable[] */
    private $userDefinedFunctions = [];

    /** @var RuleGenerator */
    private $ruleGenerator;

    public function __construct(
        TokenizerInterface $tokenizer,
        Expressions\ExpressionFactory $expressionFactory,
        RuleGenerator $ruleGenerator
    ) {
        $this->tokenizer = $tokenizer;
        $this->expressionFactory = $expressionFactory;
        $this->ruleGenerator = $ruleGenerator;
        $this->values = new SplStack();
    }

    public function parse(string $rule): string
    {
        $this->ruleGenerator->clear();
        $this->operator = null;

        foreach ($this->getTree($rule) as $token) {
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

            $this->evaluateExpression();
        }

        return $this->ruleGenerator->get();
    }

    public function assignVariables(array $variables)
    {
        $this->variables = $variables;
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

    private function assignVariableValueFromToken(BaseToken $token)
    {
        $this->ruleGenerator->flipOperatorRequired($token);
        $this->values->push($token->getValue());
    }

    private function assignOperator(BaseToken $token)
    {
        if (isset($this->operator)) {
            throw Exceptions\ParserException::unexpectedToken($token);
        } elseif ($this->values->isEmpty()) {
            throw Exceptions\ParserException::incompleteExpression($token);
        }

        $this->ruleGenerator->operatorRequired(false);
        $this->operator = $token;
    }

    private function evaluateExpression()
    {
        if (!isset($this->operator) || $this->values->count() !== 2) {
            return;
        }

        list ($rightValue, $leftValue) = $this->values;

        $this->ruleGenerator->addBoolean($this->getExpression()->evaluate($leftValue, $rightValue));
        $this->values->pop();
        $this->values->pop();

        unset($this->operator);
    }

    private function registerFunction(string $name, Closure $callback)
    {
        $this->userDefinedFunctions[$name] = $callback;
    }

    private function getExpression(): Expressions\BaseExpression
    {
        return $this->expressionFactory->createFromOperator($this->operator);
    }

    private function getTree(string $rule): AST
    {
        return new AST($this->tokenizer->tokenize($rule), $this);
    }
}
