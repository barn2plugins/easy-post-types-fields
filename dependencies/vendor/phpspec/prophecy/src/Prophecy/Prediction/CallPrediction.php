<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Prediction;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Call\Call;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Prophecy\ObjectProphecy;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Prophecy\MethodProphecy;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Argument\ArgumentsWildcard;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Argument\Token\AnyValuesToken;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Util\StringUtil;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Prophecy\Exception\Prediction\NoCallsException;
/**
 * Call prediction.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallPrediction implements PredictionInterface
{
    private $util;
    /**
     * Initializes prediction.
     *
     * @param StringUtil $util
     */
    public function __construct(StringUtil $util = null)
    {
        $this->util = $util ?: new StringUtil();
    }
    /**
     * Tests that there was at least one call.
     *
     * @param Call[]         $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws \Prophecy\Exception\Prediction\NoCallsException
     */
    public function check(array $calls, ObjectProphecy $object, MethodProphecy $method)
    {
        if (\count($calls)) {
            return;
        }
        $methodCalls = $object->findProphecyMethodCalls($method->getMethodName(), new ArgumentsWildcard(array(new AnyValuesToken())));
        if (\count($methodCalls)) {
            throw new NoCallsException(\sprintf("No calls have been made that match:\n" . "  %s->%s(%s)\n" . "but expected at least one.\n" . "Recorded `%s(...)` calls:\n%s", \get_class($object->reveal()), $method->getMethodName(), $method->getArgumentsWildcard(), $method->getMethodName(), $this->util->stringifyCalls($methodCalls)), $method);
        }
        throw new NoCallsException(\sprintf("No calls have been made that match:\n" . "  %s->%s(%s)\n" . "but expected at least one.", \get_class($object->reveal()), $method->getMethodName(), $method->getArgumentsWildcard()), $method);
    }
}
