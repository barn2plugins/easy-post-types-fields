<?php

declare (strict_types=1);
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PhpParser\Node\Expr;
class PreDec extends Expr
{
    /** @var Expr Variable */
    public $var;
    /**
     * Constructs a pre decrement node.
     *
     * @param Expr  $var        Variable
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->var = $var;
    }
    public function getSubNodeNames() : array
    {
        return ['var'];
    }
    public function getType() : string
    {
        return 'Expr_PreDec';
    }
}
