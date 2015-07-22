<?php

/**
 * @author   Nicolas Oelgart <nicolas.oelgart@non.schneider-electric.com>
 * @date     28/05/2015
 * @version  0.1
 */
namespace nicoSWD\Rules;

/**
 * Interface EvaluatorInterface
 * @package nicoSWD\Rules
 */
interface EvaluatorInterface
{
    /**
     * @param string $group
     * @return bool
     */
    public function evaluate($group);
}
