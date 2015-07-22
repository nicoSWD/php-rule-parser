<?php

/**
 * @author   Nicolas Oelgart <nicolas.oelgart@non.schneider-electric.com>
 * @version  0.2
 */
namespace nicoSWD\Rules;

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
            (?<And>(\band\b|&&))
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
        $stack = [];
        $line = 1;
        $offset = 0;
        $baseNameSpace = __NAMESPACE__ . '\\Tokens\\Token';

        while (preg_match($this->tokens, $string, $matches, 0, $offset)) {
            $token = $this->getMatchedToken($matches);
            $className = $baseNameSpace . $token;

            $stack[] = new $className(
                $matches[$token],
                $offset,
                $line
            );

            if ($token === 'Newline') {
                $line++;
            }

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
