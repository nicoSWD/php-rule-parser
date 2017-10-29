<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Evaluator;

class Evaluator implements EvaluatorInterface
{
    const LOGICAL_AND = '&';
    const LOGICAL_OR = '|';

    const BOOL_TRUE = '1';
    const BOOL_FALSE = '0';

    public function evaluate(string $group): bool
    {
        $count = 0;

        do {
            $group = preg_replace_callback(
                '~\(([^\(\)]+)\)~',
                [$this, 'evalGroup'],
                $group,
                -1,
                $count
            );
        } while ($count > 0);

        return (bool) $this->evalGroup([1 => $group]);
    }

    /**
     * @param string[] $group
     * @throws Exception\UnknownSymbolException
     * @return int|null
     */
    private function evalGroup(array $group)
    {
        $result = null;
        $operator = null;

        for ($offset = 0; isset($group[1][$offset]); $offset++) {
            $value = $group[1][$offset];

            if ($this->isLogical($value)) {
                $operator = $value;
            } elseif ($this->isBoolean($value)) {
                $result = $this->setResult($result, $value, $operator);
            } else {
                throw new Exception\UnknownSymbolException(sprintf('Unexpected "%s"', $value));
            }
        }

        return $result;
    }

    private function setResult($result, string $value, $operator): int
    {
        if (!isset($result)) {
            $result = (int) $value;
        } elseif ($operator === self::LOGICAL_AND) {
            $result &= $value;
        } elseif ($operator === self::LOGICAL_OR) {
            $result |= $value;
        }

        return $result;
    }

    private function isLogical($value): bool
    {
        return $value === self::LOGICAL_AND || $value === self::LOGICAL_OR;
    }

    private function isBoolean($value): bool
    {
        return $value === self::BOOL_TRUE || $value === self::BOOL_FALSE;
    }
}
