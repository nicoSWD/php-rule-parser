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
use nicoSWD\Rules\Core\Functions\ParseFloat;
use nicoSWD\Rules\Core\Functions\ParseInt;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\BaseToken;

class Parser
{
    /** @var array */
    public $variables = [];

    /** @var null|mixed[] */
    protected $values = null;

    /** @var null|BaseToken */
    protected $operator =  null;

    /** @var string */
    protected $output = '';

    /** @var bool */
    protected $operatorRequired = false;

    /** @var bool */
    protected $incompleteCondition = false;

    /** @var int */
    protected $openParenthesis = 0;

    /** @var int */
    protected $closedParenthesis = 0;

    /** @var TokenizerInterface */
    protected $tokenizer;

    /** @var Expressions\Factory */
    protected $expressionFactory;

    /** @var Callable[] */
    protected $userDefinedFunctions = [];

    public function __construct(TokenizerInterface $tokenizer, Expressions\Factory $expressionFactory)
    {
        $this->tokenizer = $tokenizer;
        $this->expressionFactory = $expressionFactory;

        $this->registerFunctionClass(ParseInt::class);
        $this->registerFunctionClass(ParseFloat::class);
    }

    public function parse(string $rule): string
    {
        $this->output = '';
        $this->operator = null;
        $this->values = null;
        $this->operatorRequired = false;

        foreach (new AST($this->tokenizer->tokenize($rule), $this) as $token) {
            switch ($token->getType()) {
                case TokenType::VALUE:
                    $this->assignVariableValueFromToken($token);
                    break;
                case TokenType::LOGICAL:
                    $this->assignLogicalToken($token);
                    continue 2;
                case TokenType::PARENTHESES:
                    $this->assignParentheses($token);
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

        $this->assertSyntaxSeemsOkay();

        return $this->output;
    }

    public function assignVariables(array $variables)
    {
        $this->variables = $variables;
    }

    protected function assignVariableValueFromToken(BaseToken $token)
    {
        if ($this->operatorRequired) {
            throw new Exceptions\ParserException(sprintf(
                'Missing operator at position %d on line %d',
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $this->operatorRequired = !$this->operatorRequired;
        $this->incompleteCondition = false;

        if (!isset($this->values)) {
            $this->values = [$token->getValue()];
        } else {
            $this->values[] = $token->getValue();
        }
    }

    protected function assignParentheses(BaseToken $token)
    {
        if ($token instanceof Tokens\TokenOpeningParentheses) {
            if ($this->operatorRequired) {
                throw Exceptions\ParserException::unexpectedToken($token);
            }

            $this->output .= '(';
            $this->openParenthesis++;
        } else {
            if ($this->openParenthesis < 1) {
                throw new Exceptions\ParserException(sprintf(
                    'Missing opening parenthesis at position %d on line %d',
                    $token->getPosition(),
                    $token->getLine()
                ));
            }

            $this->closedParenthesis++;
            $this->output .= ')';
        }
    }

    protected function assignLogicalToken(BaseToken $token)
    {
        if (!$this->operatorRequired) {
            throw Exceptions\ParserException::unexpectedToken($token);
        }

        if ($token instanceof Tokens\TokenAnd) {
            $this->output .= '&';
        } else {
            $this->output .= '|';
        }

        $this->incompleteCondition = true;
        $this->operatorRequired = false;
    }

    protected function assignOperator(BaseToken $token)
    {
        if (isset($this->operator)) {
            throw Exceptions\ParserException::unexpectedToken($token);
        } elseif (!isset($this->values)) {
            throw Exceptions\ParserException::incompleteExpression($token);
        }

        $this->operator = $token;
        $this->operatorRequired = false;
    }

    protected function parseExpression()
    {
        if (!isset($this->operator) || count($this->values) <> 2) {
            return;
        }

        $this->operatorRequired = true;
        $expression = $this->expressionFactory->createFromOperator($this->operator);
        $this->output .= (int) $expression->evaluate($this->values[0], $this->values[1]);

        unset($this->operator, $this->values);
    }

    protected function assertSyntaxSeemsOkay()
    {
        if ($this->incompleteCondition) {
            throw new Exceptions\ParserException(
                'Incomplete and/or condition'
            );
        } elseif ($this->openParenthesis > $this->closedParenthesis) {
            throw new Exceptions\ParserException(
                'Missing closing parenthesis'
            );
        } elseif (isset($this->operator) || (isset($this->values) && count($this->values) > 0)) {
            throw new Exceptions\ParserException(
                'Incomplete expression'
            );
        }
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
