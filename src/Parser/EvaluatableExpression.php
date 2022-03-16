<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Parser;

use nicoSWD\Rule\Expression\BaseExpression;
use nicoSWD\Rule\Expression\ExpressionFactoryInterface;
use nicoSWD\Rule\TokenStream\Token\Type\Operator;

final class EvaluatableExpression
{
    public ?Operator $operator = null;
    public array $values = [];

    public function __construct(
        private readonly ExpressionFactoryInterface $expressionFactory,
    ) {
    }

    /** @throws Exception\ParserException */
    public function evaluate(): bool
    {
        $result = $this->expression()->evaluate(...$this->values);
        $this->clear();

        return $result;
    }

    public function isComplete(): bool
    {
        return $this->hasOperator() && $this->hasBothValues();
    }

    public function addValue(mixed $value): void
    {
        $this->values[] = $value;
    }

    public function hasBothValues(): bool
    {
        return count($this->values) === 2;
    }

    public function hasNoValues(): bool
    {
        return count($this->values) === 0;
    }

    public function hasOperator(): bool
    {
        return $this->operator !== null;
    }

    private function clear(): void
    {
        $this->operator = null;
        $this->values = [];
    }

    private function expression(): BaseExpression
    {
        return $this->expressionFactory->createFromOperator($this->operator);
    }
}
