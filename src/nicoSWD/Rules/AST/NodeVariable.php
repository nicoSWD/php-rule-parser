<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class NodeVariable
 * @package nicoSWD\Rules\AST
 */
final class NodeVariable extends BaseNode
{
    /**
     * @return \nicoSWD\Rules\Tokens\BaseToken
     */
    public function getNode()
    {
        $current = $this->token->getStack()->current();

        switch (gettype($current->getValue())) {
            case 'string':
                $current = new TokenString('"' . $current->getValue() . '"', $current->getOffset(), $current->getStack());
        }

        while ($this->hasMethodCall()) {
            $method = sprintf(
                '\nicoSWD\Rules\Core\Methods\%s\%s',
                'String_',
                ucfirst($this->getMethodName())
            );

            /** @var \nicoSWD\Rules\Core\Methods\CallableMethod $instance */
            $instance = new $method();
            $current = $instance->call($current, $this->getFunctionArgs());
        }

        return $current;
    }
}
