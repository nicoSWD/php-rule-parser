<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule;

use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Parser\EvaluatableExpressionFactory;
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\Compiler\CompilerFactory;
use nicoSWD\Rule\Evaluator\Evaluator;
use nicoSWD\Rule\Evaluator\EvaluatorInterface;
use nicoSWD\Rule\Tokenizer\Tokenizer;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\TokenIteratorFactory;
use nicoSWD\Rule\TokenStream\CallableUserMethodFactory;

return new class {
    private static TokenIteratorFactory $tokenStreamFactory;
    private static TokenFactory $tokenFactory;
    private static CompilerFactory $compiler;
    private static JavaScript $javaScript;
    private static EvaluatableExpressionFactory $expressionFactory;
    private static CallableUserMethodFactory $userMethodFactory;
    private static Tokenizer $tokenizer;
    private static Evaluator $evaluator;

    public function parser(array $variables): Parser\Parser
    {
        return new Parser\Parser(
            self::ast($variables),
            self::expressionFactory(),
            self::compiler()
        );
    }

    public function evaluator(): EvaluatorInterface
    {
        if (!isset(self::$evaluator)) {
            self::$evaluator = new Evaluator();
        }

        return self::$evaluator;
    }

    private static function tokenFactory(): TokenFactory
    {
        if (!isset(self::$tokenFactory)) {
            self::$tokenFactory = new TokenFactory();
        }

        return self::$tokenFactory;
    }

    private static function compiler(): CompilerFactory
    {
        if (!isset(self::$compiler)) {
            self::$compiler = new CompilerFactory();
        }

        return self::$compiler;
    }

    private static function ast(array $variables): TokenStream
    {
        $tokenStream = new TokenStream(self::tokenizer(), self::tokenFactory(), self::tokenStreamFactory(), self::userMethodFactory());
        $tokenStream->setVariables($variables);

        return $tokenStream;
    }

    private static function tokenizer(): Tokenizer
    {
        if (!isset(self::$tokenizer)) {
            self::$tokenizer = new Tokenizer(self::javascript(), self::tokenFactory());
        }

        return self::$tokenizer;
    }

    private static function javascript(): JavaScript
    {
        if (!isset(self::$javaScript)) {
            self::$javaScript = new JavaScript();
        }

        return self::$javaScript;
    }

    private static function tokenStreamFactory(): TokenIteratorFactory
    {
        if (!isset(self::$tokenStreamFactory)) {
            self::$tokenStreamFactory = new TokenIteratorFactory();
        }

        return self::$tokenStreamFactory;
    }

    private static function expressionFactory(): EvaluatableExpressionFactory
    {
        if (!isset(self::$expressionFactory)) {
            self::$expressionFactory = new EvaluatableExpressionFactory();
        }

        return self::$expressionFactory;
    }

    private static function userMethodFactory(): CallableUserMethodFactory
    {
        if (!isset(self::$userMethodFactory)) {
            self::$userMethodFactory = new CallableUserMethodFactory();
        }

        return self::$userMethodFactory;
    }
};
