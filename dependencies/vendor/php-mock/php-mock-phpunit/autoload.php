<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies;

if (\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Rule\InvocationOrder::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Rule\InvocationOrder::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Matcher\Invocation::class);
} elseif (!\interface_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Matcher\Invocation::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_Matcher_Invocation::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Matcher\Invocation::class);
}
if (!\interface_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Invocation::class) && \interface_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_Invocation::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_Invocation::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Invocation::class);
}
if (!\interface_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\MockObject::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_MockObject::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\MockObject::class);
}
if (!\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Builder\InvocationMocker::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_Builder_InvocationMocker::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Builder\InvocationMocker::class);
}
if (\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Rule\MethodName::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Rule\MethodName::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Matcher\MethodName::class);
}
if (!\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Matcher\MethodName::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_Matcher_MethodName::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Matcher\MethodName::class);
}
if (!\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\InvocationHandler::class) && !\interface_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Stub\MatcherCollection::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_Stub_MatcherCollection::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\Stub\MatcherCollection::class);
}
if (!\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\InvocationHandler::class) && !\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\InvocationMocker::class)) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit_Framework_MockObject_InvocationMocker::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\MockObject\InvocationMocker::class);
}
if (!\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Framework\BaseTestListener::class)) {
    include __DIR__ . '/compatibility/BaseTestListener.php';
    \class_alias(phpmock\phpunit\MockDisablerPHPUnit7::class, phpmock\phpunit\MockDisabler::class);
} else {
    \class_alias(phpmock\phpunit\MockDisablerPHPUnit6::class, phpmock\phpunit\MockDisabler::class);
}
if (\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Runner\Version::class) && \version_compare(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Runner\Version::id(), '8.4.0') >= 0) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\DefaultArgumentRemoverReturnTypes84::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\DefaultArgumentRemover::class);
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\MockObjectProxyReturnTypes84::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\MockObjectProxy::class);
} elseif (\class_exists(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Runner\Version::class) && \version_compare(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHPUnit\Runner\Version::id(), '8.1.0') >= 0) {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\DefaultArgumentRemoverReturnTypes::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\DefaultArgumentRemover::class);
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\MockObjectProxyReturnTypes::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\MockObjectProxy::class);
} else {
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\DefaultArgumentRemoverNoReturnTypes::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\DefaultArgumentRemover::class);
    \class_alias(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\MockObjectProxyNoReturnTypes::class, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\phpmock\phpunit\MockObjectProxy::class);
}
