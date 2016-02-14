<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\AST\TokenCollection;

final class TokenFactory
{
    /**
     * @throws ParserException
     */
    public static function createFromPHPType($value) : BaseToken
    {
        switch ($type = gettype($value)) {
            case 'string':
                return new TokenString($value);
            case 'integer':
                return new TokenInteger($value);
            case 'boolean':
                return new TokenBool($value);
            case 'NULL':
                return new TokenNull($value);
            case 'double':
                return new TokenFloat($value);
            case 'array':
                $params = new TokenCollection();

                foreach ($value as $item) {
                    $params->attach(self::createFromPHPType($item));
                }

                return new TokenArray($params);
            default:
                throw new ParserException(sprintf(
                    'Unsupported PHP type: "%s"',
                    $type
                ));
        }
    }
}
