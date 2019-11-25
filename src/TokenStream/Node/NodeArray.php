<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Node;

use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenArray;

final class NodeArray extends BaseNode
{
    public function getNode(): BaseToken
    {
        $token = new TokenArray($this->getArrayItems());

        while ($this->hasMethodCall()) {
            $token = $this->getMethod($token)->call(...$this->getArguments());
        }

        return $token;
    }
}
