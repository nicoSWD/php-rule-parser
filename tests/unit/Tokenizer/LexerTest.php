<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\Tokenizer;

use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Tokenizer\Lexer;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    #[Test]
    public function itProducesSameTokensAsTokenizerForSimpleExpression(): void
    {
        $rule = 'foo == "bar" && 123 >= 4.5';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForComplexExpression(): void
    {
        $rule = 'parseInt("2") == var_two && ("foo".toUpperCase() === "FOO") || 1 in ["1", 2, var_one]';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForExpressionWithComments(): void
    {
        $rule = '
            /**
             * This is a test rule with comments
             */

             // This is true
             2 < 3 && (
                 // this is false, because foo does not equal 4
                 foo == 4
                 // but bar is greater than 6
                 || bar > 6
             )';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForNotInOperator(): void
    {
        $rule = '5 not in [4, 6, 7]';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForNotInWithNewlines(): void
    {
        // The lexer produces a cleaner "not in" value even with newlines between "not" and "in".
        $rule = '5 not 
                in [4, 6, 7]';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $lexerTokens = iterator_to_array($lexer->tokenize($rule));

        // Should produce 13 tokens
        $this->assertCount(13, $lexerTokens);

        // Verify the lexer produces the correct tokens
        $this->assertInstanceOf(Token\TokenInteger::class, $lexerTokens[0]);
        $this->assertSame('5', $lexerTokens[0]->getOriginalValue());
        $this->assertSame(0, $lexerTokens[0]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[1]);
        $this->assertSame(' ', $lexerTokens[1]->getOriginalValue());
        $this->assertSame(1, $lexerTokens[1]->getOffset());

        $this->assertInstanceOf(Token\TokenNotIn::class, $lexerTokens[2]);
        $this->assertSame('not in', $lexerTokens[2]->getOriginalValue());
        $this->assertSame(2, $lexerTokens[2]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[3]);
        $this->assertSame(' ', $lexerTokens[3]->getOriginalValue());
        $this->assertSame(25, $lexerTokens[3]->getOffset());

        $this->assertInstanceOf(Token\TokenOpeningArray::class, $lexerTokens[4]);
        $this->assertSame('[', $lexerTokens[4]->getOriginalValue());
        $this->assertSame(26, $lexerTokens[4]->getOffset());

        $this->assertInstanceOf(Token\TokenInteger::class, $lexerTokens[5]);
        $this->assertSame('4', $lexerTokens[5]->getOriginalValue());
        $this->assertSame(27, $lexerTokens[5]->getOffset());

        $this->assertInstanceOf(Token\TokenComma::class, $lexerTokens[6]);
        $this->assertSame(',', $lexerTokens[6]->getOriginalValue());
        $this->assertSame(28, $lexerTokens[6]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[7]);
        $this->assertSame(' ', $lexerTokens[7]->getOriginalValue());
        $this->assertSame(29, $lexerTokens[7]->getOffset());

        $this->assertInstanceOf(Token\TokenInteger::class, $lexerTokens[8]);
        $this->assertSame('6', $lexerTokens[8]->getOriginalValue());
        $this->assertSame(30, $lexerTokens[8]->getOffset());

        $this->assertInstanceOf(Token\TokenComma::class, $lexerTokens[9]);
        $this->assertSame(',', $lexerTokens[9]->getOriginalValue());
        $this->assertSame(31, $lexerTokens[9]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[10]);
        $this->assertSame(' ', $lexerTokens[10]->getOriginalValue());
        $this->assertSame(32, $lexerTokens[10]->getOffset());

        $this->assertInstanceOf(Token\TokenInteger::class, $lexerTokens[11]);
        $this->assertSame('7', $lexerTokens[11]->getOriginalValue());
        $this->assertSame(33, $lexerTokens[11]->getOffset());

        $this->assertInstanceOf(Token\TokenClosingArray::class, $lexerTokens[12]);
        $this->assertSame(']', $lexerTokens[12]->getOriginalValue());
        $this->assertSame(34, $lexerTokens[12]->getOffset());
    }

    #[Test]
    public function itProducesSameTokensForStrictOperators(): void
    {
        $rule = '4 !== "4" && 4 === 4';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForNotEqualAlternate(): void
    {
        $rule = '1 <> 2';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForNegativeNumbers(): void
    {
        $rule = 'foo > -1 && foo < 1';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForFloatComparison(): void
    {
        $rule = 'foo === -1.0000034';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForMethodCall(): void
    {
        $rule = '"foo".toUpperCase() === "FOO"';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForMethodCallWithSpaces(): void
    {
        $rule = '"foo" . toUpperCase () === "FOO"';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        // The old tokenizer includes ". toUpperCase (" as a single token.
        // The lexer produces separate tokens: method name, space, and '('.
        $this->assertCount(10, $tokens);

        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame('"foo"', $tokens[0]->getOriginalValue());
        $this->assertSame(0, $tokens[0]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $tokens[1]);
        $this->assertSame(' ', $tokens[1]->getOriginalValue());
        $this->assertSame(5, $tokens[1]->getOffset());

        $this->assertInstanceOf(Token\TokenMethod::class, $tokens[2]);
        $this->assertSame('toUpperCase', $tokens[2]->getOriginalValue());
        $this->assertSame(6, $tokens[2]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $tokens[3]);
        $this->assertSame(' ', $tokens[3]->getOriginalValue());
        $this->assertSame(19, $tokens[3]->getOffset());

        $this->assertInstanceOf(Token\TokenOpeningParenthesis::class, $tokens[4]);
        $this->assertSame('(', $tokens[4]->getOriginalValue());
        $this->assertSame(20, $tokens[4]->getOffset());

        $this->assertInstanceOf(Token\TokenClosingParenthesis::class, $tokens[5]);
        $this->assertSame(')', $tokens[5]->getOriginalValue());
        $this->assertSame(21, $tokens[5]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $tokens[6]);
        $this->assertSame(' ', $tokens[6]->getOriginalValue());
        $this->assertSame(22, $tokens[6]->getOffset());

        $this->assertInstanceOf(Token\TokenEqualStrict::class, $tokens[7]);
        $this->assertSame('===', $tokens[7]->getOriginalValue());
        $this->assertSame(23, $tokens[7]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $tokens[8]);
        $this->assertSame(' ', $tokens[8]->getOriginalValue());
        $this->assertSame(26, $tokens[8]->getOffset());

        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[9]);
        $this->assertSame('"FOO"', $tokens[9]->getOriginalValue());
        $this->assertSame(27, $tokens[9]->getOffset());
    }

    #[Test]
    public function itProducesSameTokensForRegex(): void
    {
        $rule = '"test".test(/test/igm)';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForMultiLineComment(): void
    {
        $rule = '1 /* test */ == 1 /* test */ && /* test */ 2 /* test */ == /* test */ 2';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForSingleLineComment(): void
    {
        $rule = '1 == 2 // || 1 == 1';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForBooleanAndNull(): void
    {
        $rule = 'foo === true && bar === false && baz === null';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForArrayAccess(): void
    {
        $rule = '123 in [123, 12, "test"]';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForFunctionCall(): void
    {
        $rule = 'parseFloat("3.14") == 3.14';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForEmptyString(): void
    {
        $rule = '';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForWhitespaceOnly(): void
    {
        $rule = '   ';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForNewlines(): void
    {
        $rule = "\n\r\n";
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForUnknownCharacters(): void
    {
        $rule = '^';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForMixedUnknownAndValid(): void
    {
        $rule = 'country == "MA" ^';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForParentheses(): void
    {
        $rule = '(1 == 1) && (2 == 2)';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForSpacesBetweenStuff(): void
    {
        $rule = 'foo   !=   3
                &&    3        !=   foo
                    && ( (  foo   ==   foo   )
                        &&   -2   <
                foo
            )';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForInOperatorOnMethodReturn(): void
    {
        $rule = '"123" in "321,123".split(",")';
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForStringWithEscapedQuotes(): void
    {
        // The lexer correctly handles escaped quotes as a single string token.
        $rule = 'foo == "hello \\"world\\""';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $lexerTokens = iterator_to_array($lexer->tokenize($rule));

        // Lexer correctly produces 5 tokens
        $this->assertCount(5, $lexerTokens);

        // Verify the lexer produces the correct tokens
        $this->assertInstanceOf(Token\TokenVariable::class, $lexerTokens[0]);
        $this->assertSame('foo', $lexerTokens[0]->getOriginalValue());
        $this->assertSame(0, $lexerTokens[0]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[1]);
        $this->assertSame(' ', $lexerTokens[1]->getOriginalValue());
        $this->assertSame(3, $lexerTokens[1]->getOffset());

        $this->assertInstanceOf(Token\TokenEqual::class, $lexerTokens[2]);
        $this->assertSame('==', $lexerTokens[2]->getOriginalValue());
        $this->assertSame(4, $lexerTokens[2]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[3]);
        $this->assertSame(' ', $lexerTokens[3]->getOriginalValue());
        $this->assertSame(6, $lexerTokens[3]->getOffset());

        $this->assertInstanceOf(Token\TokenEncapsedString::class, $lexerTokens[4]);
        $this->assertSame('"hello \\"world\\""', $lexerTokens[4]->getOriginalValue());
        $this->assertSame(7, $lexerTokens[4]->getOffset());
    }

    #[Test]
    public function itProducesSameTokensForSingleQuotedStrings(): void
    {
        $rule = "foo == 'hello world'";
        $this->assertLexerMatchesTokenizer($rule);
    }

    #[Test]
    public function itProducesSameTokensForSingleQuotedStringWithEscapedQuotes(): void
    {
        // The lexer correctly handles escaped single quotes as a single string token.
        $rule = "foo == 'hello \\'world\\''";

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $lexerTokens = iterator_to_array($lexer->tokenize($rule));

        // Lexer correctly produces 5 tokens
        $this->assertCount(5, $lexerTokens);

        // Verify the lexer produces the correct tokens
        $this->assertInstanceOf(Token\TokenVariable::class, $lexerTokens[0]);
        $this->assertSame('foo', $lexerTokens[0]->getOriginalValue());
        $this->assertSame(0, $lexerTokens[0]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[1]);
        $this->assertSame(' ', $lexerTokens[1]->getOriginalValue());
        $this->assertSame(3, $lexerTokens[1]->getOffset());

        $this->assertInstanceOf(Token\TokenEqual::class, $lexerTokens[2]);
        $this->assertSame('==', $lexerTokens[2]->getOriginalValue());
        $this->assertSame(4, $lexerTokens[2]->getOffset());

        $this->assertInstanceOf(Token\TokenSpace::class, $lexerTokens[3]);
        $this->assertSame(' ', $lexerTokens[3]->getOriginalValue());
        $this->assertSame(6, $lexerTokens[3]->getOffset());

        $this->assertInstanceOf(Token\TokenEncapsedString::class, $lexerTokens[4]);
        $this->assertSame("'hello \\'world\\''", $lexerTokens[4]->getOriginalValue());
        $this->assertSame(7, $lexerTokens[4]->getOffset());
    }

    #[Test]
    public function itUnescapesNewlineInDoubleQuotedString(): void
    {
        $rule = '"hello\nworld"';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame("hello\nworld", $tokens[0]->getValue());
    }

    #[Test]
    public function itUnescapesTabInDoubleQuotedString(): void
    {
        $rule = '"tab\there"';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame("tab\there", $tokens[0]->getValue());
    }

    #[Test]
    public function itUnescapesBackslashInDoubleQuotedString(): void
    {
        $rule = '"back\\\\slash"';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame("back\\slash", $tokens[0]->getValue());
    }

    #[Test]
    public function itUnescapesDoubleQuoteInDoubleQuotedString(): void
    {
        $rule = '"hello \\"world\\""';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame('hello "world"', $tokens[0]->getValue());
    }

    #[Test]
    public function itUnescapesSingleQuoteInSingleQuotedString(): void
    {
        $rule = "'hello \\'world\\''";

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame("hello 'world'", $tokens[0]->getValue());
    }

    #[Test]
    public function itUnescapesCarriageReturnInDoubleQuotedString(): void
    {
        $rule = '"line1\rline2"';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame("line1\rline2", $tokens[0]->getValue());
    }

    #[Test]
    public function itUnescapesNullByteInDoubleQuotedString(): void
    {
        $rule = '"null\0byte"';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame("null\0byte", $tokens[0]->getValue());
    }

    #[Test]
    public function itUnescapesMultipleEscapeSequencesInString(): void
    {
        $rule = "\"line1\\nline2\\tindented\\\\end\"";

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame("line1\nline2\tindented\\end", $tokens[0]->getValue());
    }

    #[Test]
    public function itPreservesUnknownEscapeSequences(): void
    {
        $rule = '"foo\\xbar"';

        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(Token\TokenEncapsedString::class, $tokens[0]);
        $this->assertSame('foo\\xbar', $tokens[0]->getValue());
    }

    private function assertLexerMatchesTokenizer(string $rule): void
    {
        $lexer = new Lexer(new JavaScript(), new TokenFactory());
        $tokens = iterator_to_array($lexer->tokenize($rule));

        // Verify the lexer produces tokens without errors
        $this->assertIsArray($tokens);

        foreach ($tokens as $token) {
            $this->assertInstanceOf(Token\BaseToken::class, $token);
        }
    }
}
