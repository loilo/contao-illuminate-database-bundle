<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Test;

use Illuminate\Support\Collection;

/**
 * Test whether non-model behavior of shadowed methods still works as intended
 */
class FallthroughTest extends Basetest
{
    /**
     * Test QueryBuilder::get()
     */
    public function testGet()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('select')
            ->once()
            ->with('select * from "mock"', [], true)
            ->andReturn([
                [ 'id' => 1 ]
            ]);

        $result = $builder->select('*')->from('mock')->get();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals([ [ 'id' => 1 ] ], $result->toArray());
    }

    /**
     * Test QueryBuilder::first()
     */
    public function testFirst()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('select')
            ->once()
            ->with('select * from "mock" limit 1', [], true)
            ->andReturn([
                [ 'id' => 1 ]
            ]);

        $result = $builder->select('*')->from('mock')->first();

        $this->assertIsArray($result);
        $this->assertEquals([ 'id' => 1 ], $result);
    }

    /**
     * Test QueryBuilder::find()
     */
    public function testFind()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('select')
            ->once()
            ->with('select * from "mock" where "id" = ? limit 1', [1], true)
            ->andReturn([
                [ 'id' => 1 ]
            ]);

        $result = $builder->select('*')->from('mock')->find(1);

        $this->assertIsArray($result);
        $this->assertEquals([ 'id' => 1 ], $result);
    }

    /**
     * Test QueryBuilder::cursor()
     */
    public function testCursor()
    {
        $builder = $this->getBuilder();

        $builder
            ->getConnection()
            ->shouldReceive('cursor')
            ->once()
            ->with('select * from "mock"', [], true)
            ->andReturn($this->createGeneratorFromArray([[ 'id' => 1 ]]));

        $result = $builder->select('*')->from('mock')->cursor();

        $this->assertInstanceOf(\Generator::class, $result);
        $this->assertEquals([[ 'id' => 1 ]], iterator_to_array($result));
    }
}
