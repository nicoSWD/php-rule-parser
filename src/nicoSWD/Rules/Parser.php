<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

/**
 * Class Parser
 * @package nicoSWD\Rules
 */
class Parser
{
    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var null|string
     */
    protected $leftValue = \null;

    /**
     * @var null|string
     */
    protected $rightValue = \null;

    /**
     * @var null|string
     */
    protected $operator =  \null;

    /**
     * @var string
     */
    protected $output = '';

    /**
     * @var bool
     */
    protected $operatorRequired = \false;

    /**
     * @var bool
     */
    protected $incompleteCondition = \false;

    /**
     * @var int
     */
    protected $openParenthesis = 0;

    /**
     * @var int
     */
    protected $closedParenthesis = 0;

    /**
     * @var TokenizerInterface
     */
    protected $tokenizer;

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @param string $rule
     * @return string
     * @throws Exceptions\ParserException
     */
    public function parse($rule)
    {
        $this->output = '';
        $this->operator = \null;
        $this->leftValue = \null;
        $this->rightValue = \null;
        $this->operatorRequired = \false;

        foreach ($this->tokenizer->tokenize($rule) as $token) {
            switch ($token->getGroup()) {
                case Constants::GROUP_VARIABLE:
                    $this->assignVariableValueFromArray($token);
                    break;
                case Constants::GROUP_VALUE:
                    $this->assignVariableValueFromToken($token);
                    break;
                case Constants::GROUP_LOGICAL:
                    $this->assignLogicalToken($token);
                    continue 2;
                case Constants::GROUP_PARENTHESES:
                    $this->assignParentheses($token);
                    continue 2;
                case Constants::GROUP_OPERATOR:
                    $this->assignOperator($token);
                    continue 2;
                case Constants::GROUP_UNKNOWN:
                    throw new Exceptions\ParserException(sprintf(
                        'Unknown token "%s" at position %d on line %d',
                        $token->getValue(),
                        $token->getPosition(),
                        $token->getLine()
                    ));
            }

            $this->parseExpression();
        }

        $this->assertSyntaxSeemsOkay();
        return $this->output;
    }

    /**
     * @param array $variables
     */
    public function assignVariables(array $variables)
    {
        $this->variables = array_change_key_case($variables, \CASE_UPPER);
    }

    /**
     * @param Tokens\BaseToken $token
     * @throws Exceptions\ParserException
     */
    protected function assignVariableValueFromArray(Tokens\BaseToken $token)
    {
        if ($this->operatorRequired) {
            throw new Exceptions\ParserException(sprintf(
                'Missing operator at position %d on line %d',
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $tokenValue = strtoupper($token->getValue());

        if (!array_key_exists($tokenValue, $this->variables)) {
            throw new Exceptions\ParserException(sprintf(
                'Undefined variable "%s" at position %d on line %d',
                $tokenValue,
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $this->incompleteCondition = \false;
        $this->operatorRequired = !$this->operatorRequired;
        $tokenValue = (string) $this->variables[$tokenValue];

        if (isset($this->leftValue)) {
            $this->rightValue = $tokenValue;
        } else {
            $this->leftValue = $tokenValue;
        }
    }

    /**
     * @param Tokens\BaseToken $token
     */
    protected function assignVariableValueFromToken(Tokens\BaseToken $token)
    {
        $this->incompleteCondition = \false;
        $this->operatorRequired = !$this->operatorRequired;

        if (!isset($this->leftValue)) {
            $this->leftValue = $token->getValue();
        } else {
            $this->rightValue = $token->getValue();
        }
    }

    /**
     * @param Tokens\BaseToken $token
     * @throws Exceptions\ParserException
     */
    protected function assignParentheses(Tokens\BaseToken $token)
    {
        if ($token instanceof Tokens\TokenOpeningParentheses) {
            if ($this->operatorRequired) {
                throw new Exceptions\ParserException(sprintf(
                    'Unexpected token "(" at position %d on line %d',
                    $token->getPosition(),
                    $token->getLine()
                ));
            }

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
        }

        $this->output .= $token->getValue();
    }

    /**
     * @param Tokens\BaseToken $token
     * @throws Exceptions\ParserException
     */
    protected function assignLogicalToken(Tokens\BaseToken $token)
    {
        if (!$this->operatorRequired) {
            throw new Exceptions\ParserException(sprintf(
                'Unexpected "%s" at position %d on line %d',
                $token->getOriginalValue(),
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $this->output .= $token->getValue();
        $this->incompleteCondition = \true;
        $this->operatorRequired = \false;
    }

    /**
     * @param Tokens\BaseToken $token
     * @throws Exceptions\ParserException
     */
    protected function assignOperator(Tokens\BaseToken $token)
    {
        if (isset($this->operator)) {
            throw new Exceptions\ParserException(sprintf(
                'Unexpected "%s" at position %d on line %d',
                $token->getOriginalValue(),
                $token->getPosition(),
                $token->getLine()
            ));
        } elseif (!isset($this->leftValue)) {
            throw new Exceptions\ParserException(sprintf(
                'Incomplete expression for token "%s" at position %d on line %d',
                $token->getOriginalValue(),
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $this->operator = $token->getValue();
        $this->operatorRequired = \false;
    }

    /**
     * @throws Exceptions\ExpressionFactoryException
     */
    protected function parseExpression()
    {
        if (!isset($this->leftValue, $this->operator, $this->rightValue)) {
            return;
        }

        $expression = Expressions\Factory::createFromOperator($this->operator);
        $result = $expression->evaluate($this->leftValue, $this->rightValue);

        $this->output .= (int) $result;
        $this->operatorRequired = \true;

        unset($this->operator, $this->leftValue, $this->rightValue);
    }

    /**
     * @throws Exceptions\ParserException
     */
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
        } elseif (isset($this->operator) || isset($this->leftValue) || isset($this->rightValue)) {
            throw new Exceptions\ParserException(
                'Incomplete expression'
            );
        }
    }
}
