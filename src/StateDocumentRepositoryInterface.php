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
use Xabbuh\XApi\Model\StateDocument;
use Xabbuh\XApi\Model\StateDocumentsFilter;
use XApi\Repository\Api\Exception\DeleteException;
use XApi\Repository\Api\Exception\SaveException;

/**
 * Public API of an Experience API (xAPI) {@link StateDocument} repository.
 *
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
interface StateDocumentRepositoryInterface
{
    /**
     * Finds a {@link StateDocument} by state id.
     *
     * @param string $stateId The state id to filter by
     * @param StateDocumentsFilter $stateDocumentsFilter additional criteria to filter by
     * @return StateDocument|null The state document
     * @throws NotFoundException if no State document with the given criteria does exist
     */
    public function find(string $stateId, StateDocumentsFilter $stateDocumentsFilter): ?StateDocument;

    /**
     * Finds a collection of {@link StateDocument State documents} filtered by the given
     * criteria.
     *
     * @param StateDocumentsFilter $stateDocumentsFilter The criteria to filter by
     * @return StateDocument[] The state documents
     */
    public function findBy(StateDocumentsFilter $stateDocumentsFilter): array;

    /**
     * Writes a {@link StateDocument} to the underlying data storage.
     *
     * @param StateDocument $stateDocument The state document to store
     * @throws SaveException When the saving failed
     */
    public function save(StateDocument $stateDocument);

    /**
     * Sets a {@link StateDocument} to be persisted later.
     *
     * @param StateDocument $stateDocument The state document to store
     */
    public function saveDeferred(StateDocument $stateDocument);

    /**
     * Delete a {@link StateDocument} from the underlying data storage.
     *
     * @param StateDocument $stateDocument The state document to delete
     * @throws DeleteException When the deletion failed
     */
    public function delete(StateDocument $stateDocument);

    /**
     * Sets a {@link StateDocument} to be deleted later.
     *
     * @param StateDocument $stateDocument The state document to delete
     */
    public function deleteDeferred(StateDocument $stateDocument);

    /**
     * Persists any deferred {@link StateDocument}.
     *
     * @throws DeleteException When the deletion of one of the deferred StateDocument failed
     * @throws SaveException   When the saving of one of the deferred StateDocument failed
     */
    public function commit();
}