<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript;

use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Grammar\InternalFunction;
use nicoSWD\Rule\Grammar\InternalMethod;

final class JavaScript extends Grammar
{
    public function getInternalFunctions(): array
    {
        return [
            new InternalFunction('parseInt', Functions\ParseInt::class),
            new InternalFunction('parseFloat', Functions\ParseFloat::class),
        ];
    }

    public function getInternalMethods(): array
    {
        return [
            new InternalMethod('charAt', Methods\CharAt::class),
            new InternalMethod('concat', Methods\Concat::class),
            new InternalMethod('indexOf', Methods\IndexOf::class),
            new InternalMethod('join', Methods\Join::class),
            new InternalMethod('replace', Methods\Replace::class),
            new InternalMethod('split', Methods\Split::class),
            new InternalMethod('substr', Methods\Substr::class),
            new InternalMethod('test', Methods\Test::class),
            new InternalMethod('toLowerCase', Methods\ToLowerCase::class),
            new InternalMethod('toUpperCase', Methods\ToUpperCase::class),
            new InternalMethod('startsWith', Methods\StartsWith::class),
            new InternalMethod('endsWith', Methods\EndsWith::class),
        ];
    }
}
