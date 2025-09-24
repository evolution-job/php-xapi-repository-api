<?php

namespace XApi\Repository\Api;

use Xabbuh\XApi\Model\State;

/**
 * Public API of an Experience API (xAPI) {@link State} repository.
 */
interface StateRepositoryInterface
{
    /**
     * @param array $criteria
     * @return State|null The state or null if no matching state has been found
     */
    public function findState(array $criteria): ?State;

    /**
     * Writes a {@link Statement} to the underlying data storage.
     *
     * @param State $state The state to store
     * @param bool $flush Whether or not to flush the managed objects
     *                    immediately (i.e. write them to the data storage)
     */
    public function storeState(State $state, bool $flush = true): void;
}