<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Api;

use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\Verb;

/**
 * Public API of an Experience API (xAPI) {@link Verb} repository.
 */
interface VerbRepositoryInterface
{
    /**
     * Finds a Verb by id, including its canonical display maintained by the LRS.
     *
     * @throws NotFoundException if no Verb with the given IRI exists
     */
    public function findVerbById(IRI $iri): ?Verb;
}
