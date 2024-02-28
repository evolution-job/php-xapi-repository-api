<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Api\Tests\Functional;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\DataFixtures\ActivityFixtures;
use Xabbuh\XApi\DataFixtures\ActorFixtures;
use Xabbuh\XApi\DataFixtures\DocumentFixtures;
use Xabbuh\XApi\Model\StateDocument;
use Xabbuh\XApi\Model\StateDocumentsFilter;
use XApi\Repository\Api\StateDocumentRepositoryInterface;

/**
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
abstract class StateDocumentRepositoryTest extends TestCase
{
    private StateDocumentRepositoryInterface $stateDocumentRepository;

    protected function setUp(): void
    {
        $this->stateDocumentRepository = $this->createStateDocumentRepositoryInterface();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function testFetchingNonExistingStateDocumentThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $stateDocumentsFilter = new StateDocumentsFilter();
        $stateDocumentsFilter->byActivity(ActivityFixtures::getIdActivity())->byAgent(ActorFixtures::getTypicalAgent());

        $this->stateDocumentRepository->find('unknown-state-id', $stateDocumentsFilter);
    }

    #[DataProvider('getStateDocument')]
    public function testCreatedStateDocumentCanBeRetrievedByOriginal(StateDocument $stateDocument): void
    {
        $this->stateDocumentRepository->save($stateDocument);

        $this->testStateDocument($stateDocument);
    }

    #[DataProvider('getStateDocument')]
    public function testDeletedStateDocumentIsDeleted(StateDocument $stateDocument): void
    {
        $this->expectException(NotFoundException::class);
        $this->stateDocumentRepository->save($stateDocument);
        $this->stateDocumentRepository->delete($stateDocument);

        $stateDocumentsFilter = new StateDocumentsFilter();
        $stateDocumentsFilter->byActivity($stateDocument->getState()->getActivity())->byAgent($stateDocument->getState()->getAgent());

        $this->stateDocumentRepository->find($stateDocument->getState()->getStateId(), $stateDocumentsFilter);
    }

    #[DataProvider('getStateDocument')]
    public function testCommitSaveDeferredStateDocument(StateDocument $stateDocument): void
    {
        $this->stateDocumentRepository->saveDeferred($stateDocument);
        $this->stateDocumentRepository->commit();

        $this->testStateDocument($stateDocument);
    }

    #[DataProvider('getStateDocument')]
    public function testCommitDeleteDeferredStateDocument(StateDocument $stateDocument): void
    {
        $this->expectException(NotFoundException::class);
        $this->stateDocumentRepository->save($stateDocument);
        $this->stateDocumentRepository->deleteDeferred($stateDocument);
        $this->stateDocumentRepository->commit();

        $stateDocumentsFilter = new StateDocumentsFilter();
        $stateDocumentsFilter->byActivity($stateDocument->getState()->getActivity())->byAgent($stateDocument->getState()->getAgent());

        $this->stateDocumentRepository->find($stateDocument->getState()->getStateId(), $stateDocumentsFilter);
    }

    public static function getStateDocument(): Iterator
    {
        yield DocumentFixtures::getStateDocument();
    }

    abstract protected function createStateDocumentRepositoryInterface();

    abstract protected function cleanDatabase();

    /**
     * @param StateDocument $stateDocument
     * @return void
     * @throws NotFoundException
     */
    private function testStateDocument(StateDocument $stateDocument): void
    {
        $stateDocumentsFilter = new StateDocumentsFilter();
        $stateDocumentsFilter->byActivity($stateDocument->getState()->getActivity())->byAgent($stateDocument->getState()->getAgent());

        $fetchedStateDocument = $this->stateDocumentRepository->find($stateDocument->getState()->getStateId(), $stateDocumentsFilter);

        $this->assertSame($stateDocument->getState()->getStateId(), $fetchedStateDocument->getState()->getStateId());
        $this->assertEquals($stateDocument->getState()->getRegistrationId(), $fetchedStateDocument->getState()->getRegistrationId());
        $this->assertTrue($stateDocument->getState()->getActivity()->equals($fetchedStateDocument->getState()->getActivity()));
        $this->assertTrue($stateDocument->getState()->getAgent()->equals($fetchedStateDocument->getState()->getAgent()));
        $this->assertEquals($stateDocument->getData(), $fetchedStateDocument->getData());
    }
}