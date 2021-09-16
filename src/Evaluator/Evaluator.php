<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Evaluator;

use Closure;

final class Evaluator implements EvaluatorInterface
{
    public function evaluate(string $group): bool
    {
        $evalGroup = $this->evalGroup();
        $count = 0;

        do {
            $group = preg_replace_callback(
                '~\((?<match>[^()]+)\)~',
                $evalGroup,
                $group,
                limit: -1,
                count: $count
            );
        } while ($count > 0);

        return (bool) $evalGroup(['match' => $group]);
    }

    private function evalGroup(): Closure
    {
        return function (array $group): ?int {
            $result = null;
            $operator = null;
            $offset = 0;

            while (isset($group['match'][$offset])) {
                $value = $group['match'][$offset++];

                if (Operator::tryFrom($value)) {
                    $operator = Operator::from($value);
                } elseif (Boolean::tryFrom($value)) {
                    $result = $this->setResult($result, (int) $value, $operator);
                } else {
                    throw new Exception\UnknownSymbolException(sprintf('Unexpected "%s"', $value));
                }
            }

            return $result;
        };
    }

    private function setResult(?int $result, int $value, ?Operator $operator): int
    {
        if (!isset($result)) {
            $result = $value;
        } elseif ($operator === Operator::LOGICAL_AND) {
            $result &= $value;
        } elseif ($operator === Operator::LOGICAL_OR) {
            $result |= $value;
        }

        return $result;
    }
}
