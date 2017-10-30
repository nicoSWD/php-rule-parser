<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use nicoSWD\Rules\TokenStream\AST;
use nicoSWD\Rules\Compiler\CompilerFactory;
use nicoSWD\Rules\Evaluator\Evaluator;
use nicoSWD\Rules\Evaluator\EvaluatorInterface;
use nicoSWD\Rules\Expressions\ExpressionFactory;
use nicoSWD\Rules\Grammar\JavaScript\JavaScript;
use nicoSWD\Rules\Tokenizer\Tokenizer;
use nicoSWD\Rules\Tokens\TokenFactory;
use nicoSWD\Rules\TokenStream\TokenStream;

return new class {
    private static $tokenStream;
    private static $tokenFactory;
    private static $compiler;
    private static $javaScript;
    private static $expressionFactory;
    private static $tokenizer;
    private static $evaluator;

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

    private static function ast(array $variables): AST
    {
        $ast = new AST(self::tokenizer(), self::tokenFactory(), self::tokenStream());
        $ast->setVariables($variables);

        return $ast;
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

    private static function tokenStream(): TokenStream
    {
        if (!isset(self::$tokenStream)) {
            self::$tokenStream = new TokenStream();
        }

        return self::$tokenStream;
    }

    private static function expressionFactory(): ExpressionFactory
    {
        if (!isset(self::$expressionFactory)) {
            self::$expressionFactory = new ExpressionFactory();
        }

        return self::$expressionFactory;
    }
};
