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
                $current = new TokenString($value);
                break;
            case 'integer':
                $current = new TokenInteger($value);
                break;
            case 'boolean':
                $current = new TokenBool($value);
                break;
            case 'NULL':
                $current = new TokenNull($value);
                break;
            case 'double':
                $current = new TokenFloat($value);
                break;
            case 'array':
                $params = new TokenCollection();

                foreach ($value as $item) {
                    $params->attach(self::createFromPHPType($item));
                }

                $current = new TokenArray($params);
                break;
            default:
                throw new ParserException(sprintf(
                    'Unsupported PHP type: "%s"',
                    $type
                ));
        }

        return $current;
    }
}
