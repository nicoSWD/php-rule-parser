<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

/**
 * Class NodeString
 * @package nicoSWD\Rules\AST
 */
final class NodeString extends BaseNode
{
    /**
     * @return \nicoSWD\Rules\Tokens\BaseToken
     */
    public function getNode()
    {
        $current = $this->token->getStack()->current();

        if ($this->hasMethodCall()) {
            return call_user_func_array(
                [$current, $this->getMethodName()],
                $this->getFunctionArgs()
            );
        }

        return $current;
    }
}
