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
        $current = $this->ast->getStack()->current();

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
