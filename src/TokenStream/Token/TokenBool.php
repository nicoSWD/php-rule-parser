<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

final class TokenBool extends BaseToken
{
    public function getType(): int
    {
        return TokenType::VALUE;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getValue()
    {
        $value = parent::getValue();

        return $value === true || strtolower((string) $value) === 'true';
    }
}
