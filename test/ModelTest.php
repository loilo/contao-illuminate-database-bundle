<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Test;

use Mockery;
use UnexpectedValueException;
use Illuminate\Support\Collection;

/**
 * Test queries with QueryBuilder::asModel()
 */
class ModelTest extends BaseTest
{
    protected static $staticContaoModel;

    public static function setUpBeforeClass(): void
    {
        $staticContaoModel = Mockery::mock('alias:Contao\\Model');
        $staticContaoModel
            ->shouldReceive('getClassFromTable')
            ->andReturnUsing(function ($table) {
                switch ($table) {
                    case 'tl_mock':
                        return Mocks\BasicMockModel::class;
                    
                    case 'tl_diverging_pk_mock':
                        return Mocks\DivergingPkMockModel::class;

                    default:
                        throw new UnexpectedValueException(sprintf(
                            'Unexpected table name "%s"',
                            $table
                        ));
                }
            });
        
        static::$staticContaoModel = $staticContaoModel;
    }

    /**
     * Test QueryBuilder::get() with unassociated primary key
     */
    public function testGetIgnoreSelect()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('select')
            ->once()
            ->with('select "id" from "mock"', [], true);

        $builder->select('some_column')->from('mock')->asModel()->get();

        $this->addToAssertionCount(1);
    }

    /**
     * Test QueryBuilder::cursor() with unassociated primary key
     */
    public function testCursorIgnoreSelect()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('cursor')
            ->once()
            ->with('select "id" from "mock"', [], true);

        $builder->select('some_column')->from('mock')->asModel()->cursor();

        $this->addToAssertionCount(1);
    }

    /**
     * Test QueryBuilder::first() with model
     */
    public function testBasicModel()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('select')
            ->once()
            ->with('select "id" from "mock" limit 1', [], true)
            ->andReturn([
                (object) [ 'id' => 1 ]
            ]);

        $model = $builder->from('mock')->asModel()->first();

        $this->assertInstanceOf(Mocks\BasicMockModel::class, $model);
        $this->assertSame(1, $model->id);
    }

    /**
     * Test QueryBuilder::first() for a
     * model with different primary key
     */
    public function testDivergingPkModel()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('select')
            ->once()
            ->with('select "identifier" from "diverging_pk_mock" limit 1', [], true)
            ->andReturn([
                (object) [ 'identifier' => 1 ]
            ]);

        $model = $builder->from('diverging_pk_mock')->asModel()->first();

        $this->assertInstanceOf(
            Mocks\DivergingPkMockModel::class,
            $model
        );
        $this->assertSame(1, $model->identifier);
    }

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
            ->with('select "id" from "mock"', [], true)
            ->andReturn([
                (object) [ 'id' => 1 ],
                (object) [ 'id' => 2 ]
            ]);
        
        $models = $builder->from('mock')->asModel()->get();

        $this->assertInstanceOf(
            Collection::class,
            $models
        );

        $this->assertSame(2, $models->count());
        $this->assertSame(1, $models->get(0)->id);
        $this->assertSame(2, $models->get(1)->id);
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
            ->with('select "id" from "mock" where "id" = ? limit 1', [1], true)
            ->andReturn([
                (object) [ 'id' => 1 ]
            ]);

        $model = $builder->from('mock')->asModel()->find(1);

        $this->assertInstanceOf(Mocks\BasicMockModel::class, $model);
        $this->assertSame(1, $model->id);
    }

    /**
     * Test QueryBuilder::find() with empty result
     */
    public function testEmptyFind()
    {
        $builder = $this->getBuilder();
        $builder
            ->getConnection()
            ->shouldReceive('select')
            ->once()
            ->with('select "id" from "mock" where "id" = ? limit 1', [1], true)
            ->andReturn([]);

        $model = $builder->from('mock')->asModel()->find(1);

        $this->assertNull($model);
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
            ->with('select "id" from "mock"', [], true)
            ->andReturn($this->createGeneratorFromArray([
                (object) [ 'id' => 1 ]
            ]));

        $modelsCursor = $builder->from('mock')->asModel()->cursor();

        $this->assertInstanceOf(\Generator::class, $modelsCursor);

        $models = iterator_to_array($modelsCursor);

        $this->assertIsArray($models);
        $this->assertSame(1, sizeof($models));
        $this->assertInstanceOf(Mocks\BasicMockModel::class, $models[0]);
        $this->assertSame(1, $models[0]->id);
    }
}
