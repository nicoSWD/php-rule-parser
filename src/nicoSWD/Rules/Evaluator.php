<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

final class Evaluator implements EvaluatorInterface
{
    public function evaluate(string $group) : bool
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
     * @throws Exceptions\EvaluatorException
     * @return int|null
     */
    private function evalGroup(array $group)
    {
        $flag = null;
        $operator = null;

        for ($offset = 0; isset($group[1][$offset]); $offset++) {
            $value = $group[1][$offset];

            if ($value === '&' || $value === '|') {
                $operator = $value;
            } elseif ($value === '1' || $value === '0') {
                if (!isset($flag)) {
                    $flag = (int) $value;
                } elseif ($operator === '&') {
                    $flag &= $value;
                } else {
                    $flag |= $value;
                }
            } else {
                throw new Exceptions\EvaluatorException(sprintf(
                    'Unexpected "%s"',
                    $value
                ));
            }
        }

        return $flag;
    }
}
