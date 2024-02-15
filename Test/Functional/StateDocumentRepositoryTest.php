<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Api\Test\Functional;

use PHPUnit\Framework\TestCase;
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
    /**
     * @var StateDocumentRepositoryInterface
     */
    private $stateDocumentRepository;

    protected function setUp()
    {
        $this->stateDocumentRepository = $this->createStateDocumentRepositoryInterface();
        $this->cleanDatabase();
    }

    protected function tearDown()
    {
        $this->cleanDatabase();
    }

    /**
     * @expectedException \Xabbuh\XApi\Common\Exception\NotFoundException
     */
    public function testFetchingNonExistingStateDocumentThrowsException()
    {
        $criteria = new StateDocumentsFilter();
        $criteria
            ->byActivity(ActivityFixtures::getIdActivity())
            ->byAgent(ActorFixtures::getTypicalAgent());

        $this->stateDocumentRepository->find('unknown-state-id', $criteria);
    }

    /**
     * @dataProvider getStateDocument
     */
    public function testCreatedStateDocumentCanBeRetrievedByOriginal(StateDocument $stateDocument)
    {
        $this->stateDocumentRepository->save($stateDocument);

        $criteria = new StateDocumentsFilter();
        $criteria
            ->byActivity($stateDocument->getState()->getActivity())
            ->byAgent($stateDocument->getState()->getActor());

        $fetchedStateDocument = $this->stateDocumentRepository->find($stateDocument->getState()->getStateId(), $criteria);

        $this->assertEquals($stateDocument->getState()->getStateId(), $fetchedStateDocument->getState()->getStateId());
        $this->assertEquals($stateDocument->getState()->getRegistrationId(), $fetchedStateDocument->getState()->getRegistrationId());
        $this->assertTrue($stateDocument->getState()->getActivity()->equals($fetchedStateDocument->getState()->getActivity()));
        $this->assertTrue($stateDocument->getState()->getActor()->equals($fetchedStateDocument->getState()->getActor()));
        $this->assertEquals($stateDocument->getData(), $fetchedStateDocument->getData());
    }

    /**
     * @dataProvider getStateDocument
     * @expectedException \Xabbuh\XApi\Common\Exception\NotFoundException
     */
    public function testDeletedStateDocumentIsDeleted(StateDocument $stateDocument)
    {
        $this->stateDocumentRepository->save($stateDocument);
        $this->stateDocumentRepository->delete($stateDocument);

        $criteria = new StateDocumentsFilter();
        $criteria
            ->byActivity($stateDocument->getState()->getActivity())
            ->byAgent($stateDocument->getState()->getActor());

        $this->stateDocumentRepository->find($stateDocument->getState()->getStateId(), $criteria);
    }

    /**
     * @dataProvider getStateDocument
     */
    public function testCommitSaveDeferredStateDocument(StateDocument $stateDocument)
    {
        $this->stateDocumentRepository->saveDeferred($stateDocument);
        $this->stateDocumentRepository->commit();

        $criteria = new StateDocumentsFilter();
        $criteria
            ->byActivity($stateDocument->getState()->getActivity())
            ->byAgent($stateDocument->getState()->getActor());

        $fetchedStateDocument = $this->stateDocumentRepository->find($stateDocument->getState()->getStateId(), $criteria);

        $this->assertEquals($stateDocument->getState()->getStateId(), $fetchedStateDocument->getState()->getStateId());
        $this->assertEquals($stateDocument->getState()->getRegistrationId(), $fetchedStateDocument->getState()->getRegistrationId());
        $this->assertTrue($stateDocument->getState()->getActivity()->equals($fetchedStateDocument->getState()->getActivity()));
        $this->assertTrue($stateDocument->getState()->getActor()->equals($fetchedStateDocument->getState()->getActor()));
        $this->assertEquals($stateDocument->getData(), $fetchedStateDocument->getData());
    }

    /**
     * @dataProvider getStateDocument
     * @expectedException \Xabbuh\XApi\Common\Exception\NotFoundException
     */
    public function testCommitDeleteDeferredStateDocument(StateDocument $stateDocument)
    {
        $this->stateDocumentRepository->save($stateDocument);
        $this->stateDocumentRepository->deleteDeferred($stateDocument);
        $this->stateDocumentRepository->commit();

        $criteria = new StateDocumentsFilter();
        $criteria
            ->byActivity($stateDocument->getState()->getActivity())
            ->byAgent($stateDocument->getState()->getActor());

        $this->stateDocumentRepository->find($stateDocument->getState()->getStateId(), $criteria);
    }

    public function getStateDocument()
    {
        return array(DocumentFixtures::getStateDocument());
    }

    abstract protected function createStateDocumentRepositoryInterface();

    abstract protected function cleanDatabase();
}