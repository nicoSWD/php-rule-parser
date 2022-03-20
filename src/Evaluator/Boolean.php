<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Evaluator;

enum Boolean: string
{
    case TRUE = '1';
    case FALSE = '0';

    final public static function fromBool(bool $bool): self
    {
        return $bool ? self::TRUE : self::FALSE;
    }
}
