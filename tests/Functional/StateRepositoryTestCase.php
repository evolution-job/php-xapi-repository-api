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

use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\DataFixtures\ActivityFixtures;
use Xabbuh\XApi\DataFixtures\ActorFixtures;
use Xabbuh\XApi\DataFixtures\StateFixtures;
use XApi\Repository\Api\StateRepositoryInterface;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class StateRepositoryTestCase extends TestCase
{
    private StateRepositoryInterface $stateRepository;

    protected function setUp(): void
    {
        $this->stateRepository = $this->createStateRepository();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function testCreatedStateCanBeRetrievedByOriginalParameter(): void
    {
        $activity = ActivityFixtures::getTypicalActivity();
        $agent = ActorFixtures::getForQueryAccountAgent();

        $state = StateFixtures::getCustomState(
            $activity,
            $agent,
            'bookmark',
            '123456',
            ['progression' => 0.5]
        );

        $this->stateRepository->storeState($state);

        $fetchedState = $this->stateRepository->findState($state);

        $this->assertNotNull($fetchedState);
        $this->assertTrue($state->equals($fetchedState));
    }

    public function testCreatedStatesCanBeRetrievedByActivityAndAgentParameter(): void
    {
        $activity = ActivityFixtures::getTypicalActivity();
        $agent = ActorFixtures::getForQueryAccountAgent();
        $registrationId = '12345678-1234-5678-8234-567812345678';

        $state0 = StateFixtures::getCustomState(
            $activity,
            $agent,
            'bookmark',
            null,
            ['progression' => 0.5]
        );

        $this->stateRepository->storeState($state0);

        $state1 = StateFixtures::getCustomState(
            $activity,
            $agent,
            'resume',
            $registrationId,
            ['foo' => 'bar']
        );

        $this->stateRepository->storeState($state1);

        $states = $this->stateRepository->findStates($state0);

        $this->assertCount(2, $states);

        $this->assertTrue($state0->equals($states[0]));
        $this->assertTrue($state1->equals($states[1]));
    }

    public function testFetchingNonExistingStateReturnNull(): void
    {
        $state = StateFixtures::getMinimalState();
        $state = $this->stateRepository->findState($state);

        $this->assertEmpty($state);
    }

    public function testRemoveStatement(): void
    {
        $state = StateFixtures::getTypicalState();
        $this->stateRepository->storeState($state);

        $foundState = $this->stateRepository->findState($state);

        $this->assertTrue($state->equals($foundState));

        $this->stateRepository->removeState($foundState);

        $this->assertNull($this->stateRepository->findState($foundState));
    }

    public function testStoreNewStatement(): void
    {
        $state = StateFixtures::getTypicalState();
        $this->stateRepository->storeState($state);

        $foundState = $this->stateRepository->findState($state);

        $this->assertTrue($state->equals($foundState));
    }

    public function testStoreExistingStatement(): void
    {
        $state = StateFixtures::getTypicalState();
        $this->stateRepository->storeState($state);

        $updatedState = StateFixtures::getCustomState(
            $state->getActivity(),
            $state->getAgent(),
            $state->getStateId(),
            $state->getRegistrationId(),
            ['progress' => 1]
        );

        $this->stateRepository->storeState($updatedState);

        $foundState = $this->stateRepository->findState($state);

        $this->assertTrue($foundState->equals($updatedState));
    }

    abstract protected function createStateRepository();

    abstract protected function cleanDatabase();
}
