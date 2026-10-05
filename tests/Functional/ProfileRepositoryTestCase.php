<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Api\Tests\Functional;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\Model\ProfileDocument;
use XApi\Repository\Api\ProfileRepositoryInterface;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
abstract class ProfileRepositoryTestCase extends TestCase
{
    private ProfileRepositoryInterface $profileRepository;

    protected function setUp(): void
    {
        $this->profileRepository = $this->createProfileRepository();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function testFetchingAnUnknownProfileReturnsNull(): void
    {
        self::assertNull($this->profileRepository->find('activity:https://example.org/activity', 'unknown'));
    }

    public function testStoredProfileCanBeRetrieved(): void
    {
        $document = $this->document('{"progress":0.5}', 'application/json');
        $this->profileRepository->store('activity:https://example.org/activity', 'resume', $document);

        self::assertEquals($document, $this->profileRepository->find('activity:https://example.org/activity', 'resume'));
    }

    public function testProfileIdsAreScopedToTheirResource(): void
    {
        $document = $this->document('{"progress":0.5}', 'application/json');
        $this->profileRepository->store('activity:https://example.org/one', 'resume', $document);
        $this->profileRepository->store('activity:https://example.org/two', 'preferences', $document);

        self::assertSame(['resume'], $this->profileRepository->findIds('activity:https://example.org/one'));
    }

    public function testProfileIdsAreFilteredByAnExclusiveSinceTimestamp(): void
    {
        $resource = 'agent:mailto:learner@example.org';
        $this->profileRepository->store($resource, 'old', $this->document('{}', 'application/json', new DateTimeImmutable('2024-01-01T00:00:00+00:00')));
        $this->profileRepository->store($resource, 'new', $this->document('{}', 'application/json', new DateTimeImmutable('2024-01-01T00:00:01+00:00')));

        self::assertSame(['new'], $this->profileRepository->findIds($resource, new DateTimeImmutable('2024-01-01T00:00:00+00:00')));
    }

    public function testStoringAnExistingProfileReplacesItsDocument(): void
    {
        $resource = 'activity:https://example.org/activity';
        $this->profileRepository->store($resource, 'resume', $this->document('old content', 'text/plain'));
        $updated = $this->document('{"page":4}', 'application/json');
        $this->profileRepository->store($resource, 'resume', $updated);

        self::assertEquals($updated, $this->profileRepository->find($resource, 'resume'));
    }

    public function testRemovingOneProfileDoesNotRemoveOtherProfiles(): void
    {
        $resource = 'activity:https://example.org/activity';
        $document = $this->document('{}', 'application/json');
        $this->profileRepository->store($resource, 'one', $document);
        $this->profileRepository->store($resource, 'two', $document);

        $this->profileRepository->remove($resource, 'one');

        self::assertNull($this->profileRepository->find($resource, 'one'));
        self::assertNotNull($this->profileRepository->find($resource, 'two'));
    }

    public function testRemovingAProfileResourceRemovesAllItsProfiles(): void
    {
        $resource = 'activity:https://example.org/activity';
        $document = $this->document('{}', 'application/json');
        $this->profileRepository->store($resource, 'one', $document);
        $this->profileRepository->store($resource, 'two', $document);

        $this->profileRepository->remove($resource);

        self::assertSame([], $this->profileRepository->findIds($resource));
    }

    protected function document(string $content, string $contentType, ?DateTimeImmutable $updated = null): ProfileDocument
    {
        return new ProfileDocument($content, $contentType, $updated ?? new DateTimeImmutable());
    }

    abstract protected function createProfileRepository(): ProfileRepositoryInterface;

    abstract protected function cleanDatabase(): void;
}
