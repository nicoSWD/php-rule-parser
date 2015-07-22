<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

/**
 * Evaluates a pre-parsed rule, such as:
 * (1&(0|0|0|0|0)&0)
 *
 * Class Evaluator
 * @package nicoSWD\Rules
 */
final class Evaluator implements EvaluatorInterface
{
    /**
     * @param string $group
     * @return bool
     */
    public function evaluate($group)
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
     * @param array $group
     * @return int
     * @throws Exceptions\EvaluatorException
     */
    private function evalGroup(array $group)
    {
        $flag = \null;
        $operator = \null;

        for ($offset = 0, $length = strlen($group[1]); $offset < $length; $offset += 1) {
            $value = $group[1][$offset];

            if (!isset($flag)) {
                $flag = (int) $value;
            } elseif ($value === '&' || $value === '|') {
                $operator = $value;
            } elseif ($value === '1' || $value === '0') {
                if ($operator === '&') {
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
