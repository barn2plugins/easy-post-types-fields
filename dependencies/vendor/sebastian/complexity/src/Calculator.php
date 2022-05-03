<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/complexity.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\Complexity;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Error;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Lexer;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\NodeTraverser;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\NodeVisitor\NameResolver;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\NodeVisitor\ParentConnectingVisitor;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Parser;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\ParserFactory;
final class Calculator
{
    /**
     * @throws RuntimeException
     */
    public function calculateForSourceFile(string $sourceFile) : ComplexityCollection
    {
        return $this->calculateForSourceString(\file_get_contents($sourceFile));
    }
    /**
     * @throws RuntimeException
     */
    public function calculateForSourceString(string $source) : ComplexityCollection
    {
        try {
            $nodes = $this->parser()->parse($source);
            \assert($nodes !== null);
            return $this->calculateForAbstractSyntaxTree($nodes);
            // @codeCoverageIgnoreStart
        } catch (Error $error) {
            throw new RuntimeException($error->getMessage(), (int) $error->getCode(), $error);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @param Node[] $nodes
     *
     * @throws RuntimeException
     */
    public function calculateForAbstractSyntaxTree(array $nodes) : ComplexityCollection
    {
        $traverser = new NodeTraverser();
        $complexityCalculatingVisitor = new ComplexityCalculatingVisitor(\true);
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ParentConnectingVisitor());
        $traverser->addVisitor($complexityCalculatingVisitor);
        try {
            /* @noinspection UnusedFunctionResultInspection */
            $traverser->traverse($nodes);
            // @codeCoverageIgnoreStart
        } catch (Error $error) {
            throw new RuntimeException($error->getMessage(), (int) $error->getCode(), $error);
        }
        // @codeCoverageIgnoreEnd
        return $complexityCalculatingVisitor->result();
    }
    private function parser() : Parser
    {
        return (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer());
    }
}
