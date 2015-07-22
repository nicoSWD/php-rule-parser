<?php

/**
 * @author   Nicolas Oelgart <nicolas.oelgart@non.schneider-electric.com>
 * @version  0.1
 */
namespace nicoSWD\Rules;

/**
 * Interface TokenizerInterface
 * @package nicoSWD\Rules
 */
interface TokenizerInterface
{
    /**
     * @param string $string
     * @return Tokens\BaseToken[]
     * @throws \Exception
     */
    public function tokenize($string);
}
