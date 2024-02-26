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
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Statement;
use Xabbuh\XApi\Model\StatementId;
use Xabbuh\XApi\Model\StatementsFilter;

/**
 * Public API of an Experience API (xAPI) {@link Statement} repository.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface StatementRepositoryInterface
{
    /**
     * Finds a {@link Statement} by id.
     *
     * @param StatementId $statementId The statement id to filter by
     * @param Actor|null  $authority   (Optional) actor that must be the authority
     *                                 of the returned statement
     *
     * @return Statement The statement
     *
     * @throws NotFoundException if no Statement with the given UUID does exist
     */
    public function findStatementById(StatementId $statementId, Actor $authority = null): Statement;

    /**
     * Finds a voided {@link Statement} by id.
     *
     * @param StatementId $voidedStatementId The voided statement id to filter
     *                                       by
     * @param Actor|null  $authority         (Optional) actor that must be the
     *                                       authority of the returned statement
     *
     * @return Statement The statement
     *
     * @throws NotFoundException if no voided Statement with the given UUID
     *                           does exist
     */
    public function findVoidedStatementById(StatementId $voidedStatementId, Actor $authority = null): Statement;

    /**
     * Finds a collection of {@link Statement Statements} filtered by the given
     * criteria.
     *
     * @param StatementsFilter $criteria  The criteria to filter by
     * @param Actor|null       $authority (Optional) actor that must be the
     *                                    authority of the returned statements
     *
     * @return Statement[] The statements
     */
    public function findStatementsBy(StatementsFilter $criteria, Actor $authority = null): array;

    /**
     * Writes a {@link Statement} to the underlying data storage.
     *
     * @param Statement $statement The statement to store
     * @param bool      $flush     Whether or not to flush the managed objects
     *                             immediately (i.e. write them to the data
     *                             storage)
     *
     * @return StatementId The id of the created Statement
     */
    public function storeStatement(Statement $statement, $flush = true): StatementId;
}
