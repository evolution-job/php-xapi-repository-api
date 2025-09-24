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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\DataFixtures\StatementFixtures;
use Xabbuh\XApi\Model\Statement;
use Xabbuh\XApi\Model\StatementId;
use XApi\Repository\Api\StatementRepositoryInterface;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class StatementRepositoryTestCase extends TestCase
{
    public const string UUID_REGEXP = '/^[a-f0-9]{8}-[a-f0-9]{4}-[1-5][a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/i';

    private StatementRepositoryInterface $statementRepository;

    protected function setUp(): void
    {
        $this->statementRepository = $this->createStatementRepository();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function testFetchingNonExistingStatementThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->statementRepository->findStatementById(StatementId::fromString('12345678-1234-5678-8234-567812345678'));
    }

    public function testFetchingStatementAsVoidedStatementThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $statement = StatementFixtures::getTypicalStatement()->withId();
        $statementId = $this->statementRepository->storeStatement($statement);

        $this->statementRepository->findVoidedStatementById($statementId);
    }

    #[DataProvider('getStatementsWithoutId')]
    public function testUuidIsGeneratedForNewStatementIfNotPresent(Statement $statement): void
    {
        $statement = $statement->withId();
        $statementId = $this->statementRepository->storeStatement($statement);

        $this->assertNotInstanceOf(StatementId::class, $statement->getId());
        $this->assertMatchesRegularExpression(self::UUID_REGEXP, $statementId->getValue());
    }

    #[DataProvider('getStatementsWithId')]
    public function testUuidIsNotGeneratedForNewStatementIfPresent(Statement $statement): void
    {
        $statementId = $this->statementRepository->storeStatement($statement);

        $this->assertEquals($statement->getId(), $statementId);
    }

    #[DataProvider('getStatementsWithId')]
    public function testCreatedStatementCanBeRetrievedByOriginalId(Statement $statement): void
    {
        $this->statementRepository->storeStatement($statement);

        if ($statement->getVerb()->isVoidVerb()) {
            $fetchedStatement = $this->statementRepository->findVoidedStatementById($statement->getId());
        } else {
            $fetchedStatement = $this->statementRepository->findStatementById($statement->getId());
        }

        $this->assertTrue($statement->equals($fetchedStatement));
    }

    #[DataProvider('getStatementsWithoutId')]
    public function testCreatedStatementCanBeRetrievedByGeneratedId(Statement $statement): void
    {
        $statement = $statement->withId();
        $statementId = $this->statementRepository->storeStatement($statement);

        if ($statement->getVerb()->isVoidVerb()) {
            $fetchedStatement = $this->statementRepository->findVoidedStatementById($statementId);
        } else {
            $fetchedStatement = $this->statementRepository->findStatementById($statementId);
        }

        $this->assertNotInstanceOf(StatementId::class, $statement->getId());
        $this->assertTrue($statement->equals($fetchedStatement->withId()));
    }

    public static function getStatementsWithId(): array
    {
        $fixtures = [];

        foreach (get_class_methods(StatementFixtures::class) as $method) {
            $statement = call_user_func([StatementFixtures::class, $method]);

            if ($statement instanceof Statement) {
                $fixtures[$method] = [$statement->withId(StatementId::fromString(StatementFixtures::DEFAULT_STATEMENT_ID))];
            }
        }

        return $fixtures;
    }

    public static function getStatementsWithoutId(): array
    {
        $fixtures = [];

        foreach (get_class_methods(StatementFixtures::class) as $method) {
            $statement = call_user_func([StatementFixtures::class, $method]);

            if ($statement instanceof Statement) {
                $fixtures[$method] = [$statement->withId()];
            }
        }

        return $fixtures;
    }

    public function testFetchingNonExistingVoidStatementThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->statementRepository->findVoidedStatementById(StatementId::fromString('12345678-1234-5678-8234-567812345678'));
    }

    public function testFetchingVoidStatementAsStatementThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $statement = StatementFixtures::getVoidingStatement()->withId();
        $statementId = $this->statementRepository->storeStatement($statement);

        $this->statementRepository->findStatementById($statementId);
    }

    public function testUuidIsGeneratedForNewVoidStatementIfNotPresent(): void
    {
        $statement = StatementFixtures::getVoidingStatement()->withId();
        $statementId = $this->statementRepository->storeStatement($statement);

        $this->assertNotInstanceOf(StatementId::class, $statement->getId());
        $this->assertMatchesRegularExpression(self::UUID_REGEXP, $statementId->getValue());
    }

    public function testUuidIsNotGeneratedForNewVoidStatementIfPresent(): void
    {
        $statement = StatementFixtures::getVoidingStatement();
        $statementId = $this->statementRepository->storeStatement($statement);

        $this->assertEquals($statement->getId(), $statementId);
    }

    public function testCreatedVoidStatementCanBeRetrievedByOriginalId(): void
    {
        $statement = StatementFixtures::getVoidingStatement();
        $this->statementRepository->storeStatement($statement);
        $fetchedStatement = $this->statementRepository->findVoidedStatementById($statement->getId());

        $this->assertTrue($statement->equals($fetchedStatement));
    }

    public function testCreatedVoidStatementCanBeRetrievedByGeneratedId(): void
    {
        $statement = StatementFixtures::getVoidingStatement()->withId();
        $statementId = $this->statementRepository->storeStatement($statement);
        $fetchedStatement = $this->statementRepository->findVoidedStatementById($statementId);

        $this->assertNotInstanceOf(StatementId::class, $statement->getId());
        $this->assertTrue($statement->equals($fetchedStatement->withId()));
    }

    abstract protected function createStatementRepository();

    abstract protected function cleanDatabase();
}
