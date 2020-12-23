<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Test;

use Mockery;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Loilo\ContaoIlluminateDatabaseBundle\Database\QueryBuilder;
use Loilo\ContaoIlluminateDatabaseBundle\Database\QueryBuilderFactory;
use PHPUnit\Framework\TestCase;

class ConfiguredConnectionTest extends TestCase
{

    /**
     * @var QueryBuilderFactory
     */
    protected $factory;

    /**
     * @var Capsule&\Mockery\MockInterface
     */
    protected $capsule;

    protected function setUp(): void
    {
        /** @var Capsule&\Mockery\MockInterface $capsule */
        $capsule = Mockery::mock(Capsule::class);

        $this->capsule = $capsule;
        $this->factory = new QueryBuilderFactory('', '', '', '', '');
        $this->factory->setCapsule($capsule);
    }

    protected function createConnection()
    {
        $connection = Mockery::mock(ConnectionInterface::class);
        $connection
            ->shouldReceive('getQueryGrammar')
            ->andReturnUsing(function () {
                return new Grammar();
            });
        $connection
            ->shouldReceive('getPostProcessor')
            ->andReturnUsing(function () {
                return new Processor();
            });
        
        return $connection;
    }

    public function testDefault()
    {
        $connection = $this->createConnection();

        $this->capsule
            ->shouldReceive('getConnection')
            ->once()
            ->withNoArgs()
            ->andReturn($connection);

        $builder = $this->factory->create();

        $this->assertSame($connection, $builder->getConnection());
    }

    public function testConfigured()
    {
        $config = [ 'prefix' => '' ];
        $connection = $this->createConnection();
    
        $this->capsule
            ->shouldReceive('addConnection')
            ->once()
            ->withArgs(function ($providedConfig) use ($config) {
                return array_intersect_key($providedConfig, $config) === $config;
            })
            ->andReturnUndefined();

        $this->capsule
            ->shouldReceive('getConnection')
            ->once()
            ->with('0')
            ->andReturn($connection);

        $builder = $this->factory->create($config);

        $this->assertSame($connection, $builder->getConnection());
    }

    public function testMultipleConfigured()
    {
        $config0 = [ 'prefix' => 'x' ];
        $config1 = [ 'prefix' => 'y' ];
        $connection0 = $this->createConnection();
        $connection1 = $this->createConnection();

        $this->capsule
            ->shouldReceive('addConnection')
            ->once()
            ->withArgs(function ($providedConfig) use ($config0) {
                return array_intersect_key($providedConfig, $config0) === $config0;
            })
            ->andReturnUndefined();

        $this->capsule
            ->shouldReceive('addConnection')
            ->once()
            ->withArgs(function ($providedConfig) use ($config1) {
                return array_intersect_key($providedConfig, $config1) === $config1;
            })
            ->andReturnUndefined();

        $this->capsule
            ->shouldReceive('getConnection')
            ->once()
            ->with('0')
            ->andReturn($connection0);

        $this->capsule
            ->shouldReceive('getConnection')
            ->once()
            ->with('1')
            ->andReturn($connection1);

        $builder0 = $this->factory->create($config0);
        $builder1 = $this->factory->create($config1);

        // Calling create() twice with the same config still only calls addConnection() once
        $builder0duplicate = $this->factory->create($config0);

        $this->assertSame($connection0, $builder0->getConnection());
        $this->assertSame($connection0, $builder0duplicate->getConnection());
        $this->assertSame($connection1, $builder1->getConnection());
    }
}
