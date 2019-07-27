<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Test\Mocks;

abstract class MockModel
{
    public static function getPk()
    {
        return 'id';
    }

    public static function findByPk($pk)
    {
        return new static($pk);
    }

    public function __construct($pk)
    {
        $field = $this::getPk();
        $this->$field = $pk;
    }
}
