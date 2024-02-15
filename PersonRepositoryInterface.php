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

use Xabbuh\XApi\Model\Agent;
use Xabbuh\XApi\Model\Person;

/**
 * Public API of an Experience API (xAPI) {@link Person} repository.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
interface PersonRepositoryInterface
{
    /**
     * Finds a {@link Person person} related to one {@link Agent agent}.
     *
     * @param Agent $agent The agent to filter by
     *
     * @return Person The related person
     */
    public function findRelatedPersonTo(Agent $agent);
}