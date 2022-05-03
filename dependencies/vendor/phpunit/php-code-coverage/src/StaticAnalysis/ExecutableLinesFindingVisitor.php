<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\SebastianBergmann\CodeCoverage\StaticAnalysis;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\ArrayDimFetch;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\Assign;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\BinaryOp;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\CallLike;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\Cast;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\Closure;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\Match_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\MethodCall;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\NullsafePropertyFetch;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\PropertyFetch;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\StaticPropertyFetch;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr\Ternary;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\MatchArm;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Scalar\Encapsed;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Break_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Case_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Catch_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Class_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\ClassMethod;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Continue_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Do_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Echo_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Else_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\ElseIf_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Expression;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Finally_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\For_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Foreach_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Goto_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\If_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Property;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Return_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Switch_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Throw_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\TryCatch;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\Unset_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Stmt\While_;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\NodeVisitorAbstract;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class ExecutableLinesFindingVisitor extends NodeVisitorAbstract
{
    /**
     * @psalm-var array<int, int>
     */
    private $executableLines = [];
    /**
     * @psalm-var array<int, int>
     */
    private $propertyLines = [];
    public function enterNode(Node $node) : void
    {
        $this->savePropertyLines($node);
        if (!$this->isExecutable($node)) {
            return;
        }
        foreach ($this->getLines($node) as $line) {
            if (isset($this->propertyLines[$line])) {
                return;
            }
            $this->executableLines[$line] = $line;
        }
    }
    /**
     * @psalm-return array<int, int>
     */
    public function executableLines() : array
    {
        \sort($this->executableLines);
        return $this->executableLines;
    }
    private function savePropertyLines(Node $node) : void
    {
        if (!$node instanceof Property && !$node instanceof Node\Stmt\ClassConst) {
            return;
        }
        foreach (\range($node->getStartLine(), $node->getEndLine()) as $index) {
            $this->propertyLines[$index] = $index;
        }
    }
    /**
     * @return int[]
     */
    private function getLines(Node $node) : array
    {
        if ($node instanceof Cast || $node instanceof PropertyFetch || $node instanceof NullsafePropertyFetch || $node instanceof StaticPropertyFetch) {
            return [$node->getEndLine()];
        }
        if ($node instanceof ArrayDimFetch) {
            if (null === $node->dim) {
                return [];
            }
            return [$node->dim->getStartLine()];
        }
        if ($node instanceof ClassMethod) {
            if ($node->name->name !== '__construct') {
                return [];
            }
            $existsAPromotedProperty = \false;
            foreach ($node->getParams() as $param) {
                if (0 !== ($param->flags & Class_::VISIBILITY_MODIFIER_MASK)) {
                    $existsAPromotedProperty = \true;
                    break;
                }
            }
            if ($existsAPromotedProperty) {
                // Only the line with `function` keyword should be listed here
                // but `nikic/php-parser` doesn't provide a way to fetch it
                return \range($node->getStartLine(), $node->name->getEndLine());
            }
            return [];
        }
        if ($node instanceof MethodCall) {
            return [$node->name->getStartLine()];
        }
        if ($node instanceof Ternary) {
            $lines = [$node->cond->getStartLine()];
            if (null !== $node->if) {
                $lines[] = $node->if->getStartLine();
            }
            $lines[] = $node->else->getStartLine();
            return $lines;
        }
        if ($node instanceof Match_) {
            return [$node->cond->getStartLine()];
        }
        if ($node instanceof MatchArm) {
            return [$node->body->getStartLine()];
        }
        if ($node instanceof Expression && ($node->expr instanceof Cast || $node->expr instanceof Match_ || $node->expr instanceof MethodCall)) {
            return [];
        }
        return [$node->getStartLine()];
    }
    private function isExecutable(Node $node) : bool
    {
        return $node instanceof Assign || $node instanceof ArrayDimFetch || $node instanceof BinaryOp || $node instanceof Break_ || $node instanceof CallLike || $node instanceof Case_ || $node instanceof Cast || $node instanceof Catch_ || $node instanceof ClassMethod || $node instanceof Closure || $node instanceof Continue_ || $node instanceof Do_ || $node instanceof Echo_ || $node instanceof ElseIf_ || $node instanceof Else_ || $node instanceof Encapsed || $node instanceof Expression || $node instanceof Finally_ || $node instanceof For_ || $node instanceof Foreach_ || $node instanceof Goto_ || $node instanceof If_ || $node instanceof Match_ || $node instanceof MatchArm || $node instanceof MethodCall || $node instanceof NullsafePropertyFetch || $node instanceof PropertyFetch || $node instanceof Return_ || $node instanceof StaticPropertyFetch || $node instanceof Switch_ || $node instanceof Ternary || $node instanceof Throw_ || $node instanceof TryCatch || $node instanceof Unset_ || $node instanceof While_;
    }
}
