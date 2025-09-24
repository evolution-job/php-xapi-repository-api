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

use Xabbuh\XApi\Model\State;

/**
 * Public API of an Experience API (xAPI) {@link State} repository.
 *
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
interface StateRepositoryInterface
{
    /**
     * @param State $state
     * @return State|null The state or null if no matching state has been found
     */
    public function findState(State $state): ?State;

    /**
     * @param State $state
     * @return array States if no matching states have been found
     */
    public function findStates(State $state): array;

    /**
     * @param State $state
     * @param bool $flush Whether or not to flush the managed objects
     *                     immediately (i.e. remove them to the data storage)
     */
    public function removeState(State $state, bool $flush = true): void;

    /**
     * Writes a {@link Statement} to the underlying data storage.
     *
     * @param State $state The state to store
     * @param bool $flush Whether or not to flush the managed objects
     *                    immediately (i.e. write them to the data storage)
     */
    public function storeState(State $state, bool $flush = true): void;
}