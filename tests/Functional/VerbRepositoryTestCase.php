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
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\Verb;
use XApi\Repository\Api\VerbRepositoryInterface;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
abstract class VerbRepositoryTestCase extends TestCase
{
    private VerbRepositoryInterface $verbRepository;

    protected function setUp(): void
    {
        $this->verbRepository = $this->createVerbRepository();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function testFetchingAnUnknownVerbThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->verbRepository->findVerbById(IRI::fromString('https://example.org/verbs/unknown'));
    }

    public function testKnownVerbIsRetrievedWithItsCanonicalDisplay(): void
    {
        $verb = $this->givenKnownVerb();
        $foundVerb = $this->verbRepository->findVerbById($verb->getId());

        self::assertTrue($verb->equals($foundVerb));
    }

    abstract protected function createVerbRepository(): VerbRepositoryInterface;

    abstract protected function cleanDatabase(): void;

    abstract protected function givenKnownVerb(): Verb;
}
