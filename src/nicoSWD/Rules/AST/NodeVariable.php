<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Tokens;

/**
 * Class NodeVariable
 * @package nicoSWD\Rules\AST
 */
final class NodeVariable extends BaseNode
{
    /**
     * @return Tokens\BaseToken
     */
    public function getNode()
    {
        $current = $this->ast->getStack()->current();
        $value = $this->ast->getVariable($current->getValue());

        switch (gettype($value)) {
            case 'string':
                $current = new Tokens\TokenString('"' . $value . '"');
                break;
            case 'integer':
                $current = new Tokens\TokenInteger($value);
                break;
            case 'boolean':
                $current = new Tokens\TokenBool($value);
                break;
            case 'NULL':
                $current = new Tokens\TokenNull($value);
                break;
            case 'double':
                $current = new Tokens\TokenFloat($value);
                break;
            case 'array':
                $current = new Tokens\TokenArray($value);
                break;
        }

        $current->setOffset($current->getOffset());
        $current->setStack($this->ast->getStack());

        while ($this->hasMethodCall()) {
            $current = $this->getMethod($current)->call($this->getFunctionArgs());
        }

        return $current;
    }
}
