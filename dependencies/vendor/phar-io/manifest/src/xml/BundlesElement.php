<?php

declare (strict_types=1);
/*
 * This file is part of PharIo\Manifest.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PharIo\Manifest;

class BundlesElement extends ManifestElement
{
    public function getComponentElements() : ComponentElementCollection
    {
        return new ComponentElementCollection($this->getChildrenByName('component'));
    }
}