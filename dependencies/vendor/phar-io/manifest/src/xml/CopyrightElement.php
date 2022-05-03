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

class CopyrightElement extends ManifestElement
{
    public function getAuthorElements() : AuthorElementCollection
    {
        return new AuthorElementCollection($this->getChildrenByName('author'));
    }
    public function getLicenseElement() : LicenseElement
    {
        return new LicenseElement($this->getChildByName('license'));
    }
}