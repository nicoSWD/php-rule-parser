<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Exception\ForbiddenMethodException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedFunctionException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedMethodException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedVariableException;
use nicoSWD\Rule\TokenStream\Token\TokenArray;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\TokenStream;

final readonly class AstEvaluator
{
    public function __construct(
        private TokenStream  $tokenStream,
        private TokenFactory $tokenFactory,
    ) {
    }

    /**
     * @throws ParserException
     */
    public function evaluate(Node $node): bool
    {
        return (bool) $this->resolve($node);
    }

    /**
     * Resolve a node to its actual computed value, without casting to bool.
     *
     * @throws ParserException
     */
    public function resolve(Node $node): mixed
    {
        return match ($node::class) {
            LogicalNode::class => $this->evaluateLogical($node),
            ComparisonNode::class => $this->evaluateComparison($node),
            AdditionNode::class, SubtractionNode::class, MultiplicationNode::class,
            DivisionNode::class, ModuloNode::class, MethodCallNode::class, FunctionCallNode::class => $this->resolveValue($node),
            BoolNode::class => $node->value,
            NullNode::class => null,
            IntegerNode::class => $node->value,
            FloatNode::class => $node->value,
            StringNode::class => $node->value,
            ArrayNode::class => $this->resolveArray($node),
            VariableNode::class => $this->resolveValue($node),
            UnaryMinusNode::class => $this->resolveUnaryMinus($node),
            NotNode::class => $this->resolveNot($node),
            default => throw new \RuntimeException('Unexpected root node type: ' . $node::class),
        };
    }

    /**
     * @throws ParserException
     */
    private function evaluateLogical(LogicalNode $node): bool
    {
        $left = $this->evaluate($node->left);

        // Short-circuit evaluation
        return match ($node->operator) {
            LogicalOperator::AND => $left && $this->evaluate($node->right),
            LogicalOperator::OR => $left || $this->evaluate($node->right),
        };
    }

    /**
     * @throws ParserException
     */
    private function evaluateComparison(ComparisonNode $node): bool
    {
        $leftValue = $this->resolveValue($node->left);
        $rightValue = $this->resolveValue($node->right);

        return match ($node->operator) {
            ComparisonOperator::EQUAL => $leftValue == $rightValue,
            ComparisonOperator::EQUAL_STRICT => $leftValue === $rightValue,
            ComparisonOperator::NOT_EQUAL => $leftValue != $rightValue,
            ComparisonOperator::NOT_EQUAL_STRICT => $leftValue !== $rightValue,
            ComparisonOperator::LESS_THAN => $leftValue < $rightValue,
            ComparisonOperator::GREATER_THAN => $leftValue > $rightValue,
            ComparisonOperator::LESS_THAN_EQUAL => $leftValue <= $rightValue,
            ComparisonOperator::GREATER_THAN_EQUAL => $leftValue >= $rightValue,
            ComparisonOperator::IN => $this->evaluateIn($leftValue, $rightValue),
            ComparisonOperator::NOT_IN => !$this->evaluateIn($leftValue, $rightValue),
        };
    }

    /**
     * @throws ParserException
     */
    private function evaluateIn(mixed $leftValue, mixed $rightValue): bool
    {
        if (!is_array($rightValue)) {
            throw ParserException::expectedArray(gettype($rightValue));
        }

        return in_array($leftValue, $rightValue, strict: true);
    }

    /**
     * @throws ParserException
     */
    private function resolveValue(Node $node): mixed
    {
        return match ($node::class) {
            StringNode::class => $node->value,
            IntegerNode::class => $node->value,
            FloatNode::class => $node->value,
            BoolNode::class => $node->value,
            NullNode::class => null,
            RegexNode::class => $node->pattern,
            VariableNode::class => $this->resolveVariable($node),
            ArrayNode::class => $this->resolveArray($node),
            FunctionCallNode::class => $this->resolveFunction($node),
            MethodCallNode::class => $this->resolveMethod($node),
            AdditionNode::class => $this->resolveAddition($node),
            SubtractionNode::class => $this->resolveSubtraction($node),
            MultiplicationNode::class => $this->resolveMultiplication($node),
            DivisionNode::class => $this->resolveDivision($node),
            ModuloNode::class => $this->resolveModulo($node),
            UnaryMinusNode::class => $this->resolveUnaryMinus($node),
            NotNode::class => $this->resolveNot($node),
            ComparisonNode::class => $this->evaluateComparison($node),
            LogicalNode::class => $this->evaluateLogical($node),
            default => throw new \RuntimeException('Unexpected node type: ' . $node::class),
        };
    }

    /**
     * @throws ParserException
     */
    private function resolveUnaryMinus(UnaryMinusNode $node): mixed
    {
        $value = $this->resolveValue($node->node);

        return -$value;
    }

    /**
     * @throws ParserException
     */
    private function resolveNot(NotNode $node): mixed
    {
        $value = $this->resolveValue($node->node);

        return !$value;
    }

    /**
     * @throws ParserException
     */
    private function resolveAddition(AdditionNode $node): mixed
    {
        $left = $this->resolveValue($node->left);
        $right = $this->resolveValue($node->right);

        if (is_string($left) || is_string($right)) {
            return (string) $left . (string) $right;
        }

        return $left + $right;
    }

    /**
     * @throws ParserException
     */
    private function resolveSubtraction(SubtractionNode $node): mixed
    {
        $left = $this->resolveValue($node->left);
        $right = $this->resolveValue($node->right);

        return $left - $right;
    }

    /**
     * @throws ParserException
     */
    private function resolveMultiplication(MultiplicationNode $node): mixed
    {
        $left = $this->resolveValue($node->left);
        $right = $this->resolveValue($node->right);

        return $left * $right;
    }

    /**
     * @throws ParserException
     */
    private function resolveDivision(DivisionNode $node): mixed
    {
        $left = $this->resolveValue($node->left);
        $right = $this->resolveValue($node->right);

        return $left / $right;
    }

    /**
     * @throws ParserException
     */
    private function resolveModulo(ModuloNode $node): mixed
    {
        $left = $this->resolveValue($node->left);
        $right = $this->resolveValue($node->right);

        return $left % $right;
    }

    /**
     * @throws ParserException
     */
    private function resolveVariable(VariableNode $node): mixed
    {
        try {
            $token = $this->tokenStream->getVariable($node->name);
        } catch (UndefinedVariableException) {
            throw ParserException::undefinedVariable($node->name, $node->offset);
        }

        if ($token instanceof TokenArray) {
            return $token->toArray();
        }

        return $token->getValue();
    }

    /**
     * @throws ParserException
     */
    private function resolveArray(ArrayNode $node): array
    {
        $items = [];

        foreach ($node->items as $item) {
            $items[] = $this->resolveValue($item);
        }

        return $items;
    }

    /**
     * @throws ParserException
     */
    private function resolveFunction(FunctionCallNode $node): mixed
    {
        $args = $this->resolveArguments($node->arguments);

        try {
            $closure = $this->tokenStream->getFunction($node->name);
        } catch (UndefinedFunctionException) {
            throw ParserException::undefinedFunction($node->name, $node->offset);
        }

        return $closure(...$args)->getValue();
    }

    /**
     * @throws ParserException
     */
    private function resolveMethod(MethodCallNode $node): mixed
    {
        $objectValue = $this->resolveValue($node->object);
        $objectToken = $this->tokenFactory->createFromPHPType($objectValue);

        // For regex nodes, use the original token to preserve type information
        if ($node->object instanceof RegexNode) {
            $objectToken = $node->object->originalToken;
        }

        $args = $this->resolveArguments($node->arguments);

        try {
            $method = $this->tokenStream->getMethod($node->name, $objectToken);
        } catch (UndefinedMethodException) {
            throw ParserException::undefinedMethod($node->name, $node->offset);
        } catch (ForbiddenMethodException) {
            throw ParserException::forbiddenMethod($node->name, $objectToken);
        }

        $result = $method->call(...$args);

        if ($result instanceof TokenArray) {
            return $result->toArray();
        }

        return $result->getValue();
    }

    /**
     * @throws ParserException
     */
    private function resolveArguments(array $arguments): array
    {
        $resolved = [];

        foreach ($arguments as $argument) {
            // Preserve the original token for regex arguments
            if ($argument instanceof RegexNode) {
                $resolved[] = $argument->originalToken;
                continue;
            }

            $value = $this->resolveValue($argument);
            $resolved[] = $this->tokenFactory->createFromPHPType($value);
        }

        return $resolved;
    }
}
