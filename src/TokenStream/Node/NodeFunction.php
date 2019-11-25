<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Node;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

final class NodeFunction extends BaseNode
{
    public function getNode(): BaseToken
    {
        return $this->getFunction()->call($this, ...$this->getArguments());
    }
}
