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

    public function testFetchingNonExistingStateReturnNull(): void
    {
        $state = StateFixtures::getMinimalState();
        $state = $this->stateRepository->findState([
            'stateId'        => $state->getStateId(),
            'activityId'     => $state->getActivity()->getId()->getValue(),
            'registrationId' => $state->getRegistrationId()
        ]);

        $this->assertEmpty($state);
    }

    public function testStoreNewStatement(): void
    {
        $state = StateFixtures::getTypicalState();
        $this->stateRepository->storeState($state);

        $loadedState = $this->stateRepository->findState([
            'stateId'        => $state->getStateId(),
            'activityId'     => $state->getActivity()->getId()->getValue(),
            'registrationId' => $state->getRegistrationId()
        ]);

        $this->assertEquals($state->getActivity(), $loadedState->getActivity());
        $this->assertEquals($state->getData(), $loadedState->getData());
        $this->assertEquals($state->getRegistrationId(), $loadedState->getRegistrationId());
        $this->assertEquals($state->getStateId(), $loadedState->getStateId());
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

        $this->stateRepository->storeState($state);

        $this->assertEquals($state->getActivity(), $updatedState->getActivity());
        $this->assertEquals($state->getRegistrationId(), $updatedState->getRegistrationId());
        $this->assertEquals($state->getStateId(), $updatedState->getStateId());
        $this->assertNotEquals($state->getData(), $updatedState->getData());
    }

    abstract protected function createStateRepository();

    abstract protected function cleanDatabase();
}
