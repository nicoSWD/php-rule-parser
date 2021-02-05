<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Evaluator;

final class Evaluator implements EvaluatorInterface
{
    private const LOGICAL_AND = '&';
    private const LOGICAL_OR = '|';

    private const BOOL_TRUE = '1';
    private const BOOL_FALSE = '0';

    public function evaluate(string $group): bool
    {
        $evalGroup = $this->evalGroup();
        $count = 0;

        do {
            $group = preg_replace_callback(
                '~\(([^()]+)\)~',
                $evalGroup,
                $group,
                limit: -1,
                count: $count
            );
        } while ($count > 0);

        return (bool) $evalGroup([1 => $group]);
    }

    private function evalGroup(): callable
    {
        return function (array $group): ?int {
            $result = null;
            $operator = null;
            $offset = 0;

            while (isset($group[1][$offset])) {
                $value = $group[1][$offset++];

                if ($this->isLogical($value)) {
                    $operator = $value;
                } elseif ($this->isBoolean($value)) {
                    $result = $this->setResult($result, (int) $value, $operator);
                } else {
                    throw new Exception\UnknownSymbolException(sprintf('Unexpected "%s"', $value));
                }
            }

            return $result;
        };
    }

    private function setResult(?int $result, int $value, ?string $operator): int
    {
        if (!isset($result)) {
            $result = $value;
        } elseif ($operator === self::LOGICAL_AND) {
            $result &= $value;
        } elseif ($operator === self::LOGICAL_OR) {
            $result |= $value;
        }

        return $result;
    }

    private function isLogical(string $value): bool
    {
        return $value === self::LOGICAL_AND || $value === self::LOGICAL_OR;
    }

    private function isBoolean(string $value): bool
    {
        return $value === self::BOOL_TRUE || $value === self::BOOL_FALSE;
    }
}
