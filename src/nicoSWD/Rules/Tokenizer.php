<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

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

    public function tokenize(string $string) : Stack
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

    private function getMatchedToken(array $matches) : string
    {
        foreach ($matches as $key => $value) {
            if ($value !== '' && !is_int($key)) {
                return $key;
            }
        }

        return 'Unknown';
    }
}
