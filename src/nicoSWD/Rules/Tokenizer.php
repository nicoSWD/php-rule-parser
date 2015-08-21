<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
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
            (?<And>&&)
            | (?<Or>\|\|)
            | (?<NotEqualStrict>!==)
            | (?<NotEqual><>|!=)
            | (?<EqualStrict>===)
            | (?<Equal>==)
            | (?<In>\bin\b)
            | (?<Bool>\b(?:true|false)\b)
            | (?<Null>\bnull\b)
            | (?<Method>\.\s*[a-zA-Z_]\w*\s*\()
            | (?<Function>[a-zA-Z_]\w*\s*\()
            | (?<Variable>[a-zA-Z_]\w*)
            | (?<Float>-?\d+(?:\.\d+))
            | (?<Integer>-?\d+)
            | (?<EncapsedString>"[^"]*"|\'[^\']*\')
            | (?<SmallerEqual><=)
            | (?<GreaterEqual>>=)
            | (?<Smaller><)
            | (?<Greater>>)
            | (?<OpeningParentheses>\()
            | (?<ClosingParentheses>\))
            | (?<OpeningArray>\[)
            | (?<ClosingArray>\])
            | (?<Comma>,)
            | (?<Regex>/[^/\*].*/[igm]{0,3})
            | (?<Comment>//[^\r\n]*|/\*.*?\*/)
            | (?<Newline>\r?\n)
            | (?<Space>\s+)
            | (?<Unknown>.)
        )~xAs';

    /**
     * @param string $string
     * @return Stack
     */
    public function tokenize($string)
    {
        $stack = new Stack();
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
     * @param string[] $matches
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
