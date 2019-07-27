<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Test;

use Mockery;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Loilo\ContaoIlluminateDatabaseBundle\Database\QueryBuilder;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /**
     * Get a QueryBuilder instance with mocked connection
     *
     * @return QueryBuilder
     */
    protected function getBuilder()
    {
        /** @var ConnectionInterface&\Mockery\MockInterface $connection */
        $connection = Mockery::mock(ConnectionInterface::class);

        return new QueryBuilder($connection, new Grammar(), new Processor());
    }

    /**
     * Create a generator from an array of values
     *
     * @param array $array The array to convert
     * @return \Generator
     */
    protected function createGeneratorFromArray(array $array)
    {
        foreach ($array as $value) {
            yield $value;
        }
    }
}
