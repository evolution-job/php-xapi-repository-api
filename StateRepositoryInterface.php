<?php

namespace XApi\Repository\Api;

use Xabbuh\XApi\Model\State;

/**
 * Public API of an Experience API (xAPI) {@link State} repository.
 */
interface StateRepositoryInterface
{
    /**
     * @return State The statement or null if no matching statement has been found
     */
    public function findState(array $criteria);

    /**
     * Writes a {@link Statement} to the underlying data storage.
     *
     * @param State $state The statement to store
     * @param bool $flush Whether or not to flush the managed objects
     *                    immediately (i.e. write them to the data storage)
     * @return State The id of the created Statement
     */
    public function storeState(State $state, bool $flush = true);
}