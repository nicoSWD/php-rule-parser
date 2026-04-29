<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Grammar\JavaScript;

use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Grammar\InternalCallable;

final class JavaScript extends Grammar
{
    public function getInternalFunctions(): array
    {
        return [
            new InternalCallable('parseInt', Functions\ParseInt::class),
            new InternalCallable('parseFloat', Functions\ParseFloat::class),
        ];
    }

    public function getInternalMethods(): array
    {
        return [
            new InternalCallable('charAt', Methods\CharAt::class),
            new InternalCallable('concat', Methods\Concat::class),
            new InternalCallable('indexOf', Methods\IndexOf::class),
            new InternalCallable('join', Methods\Join::class),
            new InternalCallable('replace', Methods\Replace::class),
            new InternalCallable('split', Methods\Split::class),
            new InternalCallable('substr', Methods\Substr::class),
            new InternalCallable('test', Methods\Test::class),
            new InternalCallable('toLowerCase', Methods\ToLowerCase::class),
            new InternalCallable('toUpperCase', Methods\ToUpperCase::class),
            new InternalCallable('startsWith', Methods\StartsWith::class),
            new InternalCallable('endsWith', Methods\EndsWith::class),
        ];
    }
}
