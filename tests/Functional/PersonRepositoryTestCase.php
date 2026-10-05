<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Api\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\DataFixtures\ActorFixtures;
use Xabbuh\XApi\Model\Agent;
use Xabbuh\XApi\Model\Person;
use XApi\Repository\Api\PersonRepositoryInterface;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
abstract class PersonRepositoryTestCase extends TestCase
{
    private PersonRepositoryInterface $personRepository;

    protected function setUp(): void
    {
        $this->personRepository = $this->createPersonRepository();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function testAnUnknownAgentHasNoRelatedPerson(): void
    {
        self::assertNull($this->personRepository->findRelatedPersonTo(ActorFixtures::getTypicalAgent()));
    }

    public function testKnownAgentReturnsItsRelatedPerson(): void
    {
        $agent = $this->givenKnownAgent();

        self::assertEquals($this->expectedPersonFor($agent), $this->personRepository->findRelatedPersonTo($agent));
    }

    abstract protected function createPersonRepository(): PersonRepositoryInterface;

    abstract protected function cleanDatabase(): void;

    abstract protected function givenKnownAgent(): Agent;

    abstract protected function expectedPersonFor(Agent $agent): Person;
}
