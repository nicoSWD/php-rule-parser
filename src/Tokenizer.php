<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use SplObjectStorage;

/**
 * Class Tokenizer
 * @package nicoSWD\Rules
 */
final class Tokenizer implements TokenizerInterface
{
    /**
     * @var string
     */
    private $tokens = '
        ~(
            (?<And>(?:\band\b|&&))
            | (?<Or>(?:\bor\b|\|\|))
            | (?<NotEqual><>|!==?|\bis[\s\r\n]+not\b)
            | (?<Equal>={1,3}|\bis\b)
            | (?<Variable>[a-z_]\w*)
            | (?<Number>-?\d+(?:\.\d+)?)
            | (?<String>"[^"]*"|\'[^\']*\')
            | (?<SmallerEqual><=)
            | (?<GreaterEqual>>=)
            | (?<Smaller><)
            | (?<Greater>>)
            | (?<OpeningParentheses>\()
            | (?<ClosingParentheses>\))
            | (?<Comment>(?://|\#)[^\r\n]*|/\*.*?\*/)
            | (?<Newline>\r?\n)
            | (?<Space>\s+)
            | (?<Unknown>.)
        )~xAsi';

    /**
     * @param string $string
     * @return Tokens\BaseToken[]
     */
    public function tokenize($string)
    {
        $stack = new SplObjectStorage();
        $baseNameSpace = __NAMESPACE__ . '\\Tokens\\Token';
        $offset = 0;

        while (preg_match($this->tokens, $string, $matches, 0, $offset)) {
            $token = $this->getMatchedToken($matches);
            $className = $baseNameSpace . $token;

            $stack->attach(new $className(
                $matches[$token],
                $offset,
                $stack
            ));

            $offset += strlen($matches[0]);
        }

        return $stack;
    }

    /**
     * @param array $matches
     * @return string
     */
    private function getMatchedToken(array $matches)
    {
        foreach ($matches as $key => $value) {
            if ($value !== '' && !is_int($key)) {
                return $key;
            }
        }

        return 'Unknown';
    }
}
