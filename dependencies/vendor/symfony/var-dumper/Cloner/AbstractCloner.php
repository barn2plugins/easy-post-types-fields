<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Symfony\Component\VarDumper\Cloner;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Symfony\Component\VarDumper\Caster\Caster;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = ['__PHP_Incomplete_Class' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\Caster', 'castPhpIncompleteClass'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\CutStub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\CutArrayStub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castCutArray'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ConstStub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\EnumStub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castEnum'], 'Fiber' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\FiberCaster', 'castFiber'], 'Closure' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClosure'], 'Generator' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castZendExtension'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Doctrine\\Common\\Persistence\\ObjectManager' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Doctrine\\Common\\Proxy\\Proxy' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castCommonProxy'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Doctrine\\ORM\\Proxy\\Proxy' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castOrmProxy'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Doctrine\\ORM\\PersistentCollection' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castPersistentCollection'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Doctrine\\Persistence\\ObjectManager' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'DOMException' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castException'], 'DOMStringList' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNameList' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMImplementation' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNode' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNode'], 'DOMNameSpaceNode' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNameSpaceNode'], 'DOMDocument' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocument'], 'DOMNodeList' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNamedNodeMap' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMCharacterData' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castCharacterData'], 'DOMAttr' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castAttr'], 'DOMElement' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castElement'], 'DOMText' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castText'], 'DOMTypeinfo' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castTypeinfo'], 'DOMDomError' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDomError'], 'DOMLocator' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLocator'], 'DOMDocumentType' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocumentType'], 'DOMNotation' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNotation'], 'DOMEntity' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castEntity'], 'DOMProcessingInstruction' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castProcessingInstruction'], 'DOMXPath' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castXPath'], 'XMLReader' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castErrorException'], 'Exception' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castException'], 'Error' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castError'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Bridge\\Monolog\\Logger' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\HttpClient\\AmpHttpClient' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\HttpClient\\CurlHttpClient' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\HttpClient\\NativeHttpClient' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\HttpClient\\Response\\AmpResponse' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\HttpClient\\Response\\CurlResponse' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\HttpClient\\Response\\NativeResponse' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\HttpFoundation\\Request' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castRequest'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\Uid\\Ulid' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUlid'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\Uid\\Uuid' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUuid'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Exception\\ThrowingCasterException' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castThrowingCasterException'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\TraceStub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castTraceStub'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\FrameStub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castFrameStub'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Cloner\\AbstractCloner' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\ErrorHandler\\Exception\\SilencedErrorContext' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castSilencedErrorContext'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Imagine\\Image\\ImageInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ImagineCaster', 'castImage'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Ramsey\\Uuid\\UuidInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\UuidCaster', 'castRamseyUuid'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\ProxyManager\\Proxy\\ProxyInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\PHPUnit\\Framework\\MockObject\\MockObject' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\PHPUnit\\Framework\\MockObject\\Stub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Prophecy\\Prophecy\\ProphecySubjectInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Mockery\\MockInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PDO' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdo'], 'PDOStatement' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileInfo'], 'SplFileObject' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileObject'], 'SplHeap' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'SplObjectStorage' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'OuterIterator' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castOuterIterator'], 'WeakReference' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castWeakReference'], 'Redis' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedis'], 'RedisArray' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castDateTime'], 'DateInterval' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castInterval'], 'DateTimeZone' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castTimeZone'], 'DatePeriod' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castPeriod'], 'GMP' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\GmpCaster', 'castGmp'], 'MessageFormatter' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\MemcachedCaster', 'castMemcached'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Ds\\Collection' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castCollection'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Ds\\Map' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castMap'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Ds\\Pair' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPair'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DsPairStub' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPairStub'], 'mysqli_driver' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\MysqliCaster', 'castMysqliDriver'], 'CurlHandle' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':curl' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':dba' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], ':dba persistent' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], 'GdImage' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':gd' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':mysql link' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castMysqlLink'], ':pgsql large object' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLargeObject'], ':pgsql link' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql link persistent' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql result' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castResult'], ':process' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castProcess'], ':stream' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], 'OpenSSLCertificate' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':OpenSSL X.509' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':persistent stream' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], ':stream-context' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], ':xml' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], 'RdKafka' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castRdKafka'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\Conf' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castConf'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\KafkaConsumer' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castKafkaConsumer'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\Metadata\\Broker' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castBrokerMetadata'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\Metadata\\Collection' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castCollectionMetadata'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\Metadata\\Partition' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castPartitionMetadata'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\Metadata\\Topic' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicMetadata'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\Message' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castMessage'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\Topic' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopic'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\TopicPartition' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicPartition'], 'Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\RdKafka\\TopicConf' => ['Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Barn2\\Plugin\\Easy_Post_Types_Fields\\Dependencies\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicConf']];
    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;
    /**
     * @var array<string, list<callable>>
     */
    private $casters = [];
    /**
     * @var callable|null
     */
    private $prevErrorHandler;
    private $classInfo = [];
    private $filter = 0;
    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
    }
    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }
    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }
    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString)
    {
        $this->maxString = $maxString;
    }
    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth)
    {
        $this->minDepth = $minDepth;
    }
    /**
     * Clones a PHP variable.
     *
     * @param mixed $var    Any PHP variable
     * @param int   $filter A bit field of Caster::EXCLUDE_* constants
     *
     * @return Data
     */
    public function cloneVar($var, int $filter = 0)
    {
        $this->prevErrorHandler = \set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }
            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }
            return \false;
        });
        $this->filter = $filter;
        if ($gc = \gc_enabled()) {
            \gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                \gc_enable();
            }
            \restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }
    /**
     * Effectively clones the PHP variable.
     *
     * @param mixed $var Any PHP variable
     *
     * @return array
     */
    protected abstract function doClone($var);
    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array
     */
    protected function castObject(Stub $stub, bool $isNested)
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (\PHP_VERSION_ID < 80000 ? "\x00" === ($class[15] ?? null) : \str_contains($class, "@anonymous\x00")) {
            $stub->class = \get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = \method_exists($class, '__debugInfo');
            foreach (\class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (\class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';
            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array
     */
    protected function castResource(Stub $stub, bool $isNested)
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;
        try {
            if (!empty($this->casters[':' . $type])) {
                foreach ($this->casters[':' . $type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
