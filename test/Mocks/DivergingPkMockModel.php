<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Test\Mocks;

class DivergingPkMockModel extends MockModel
{
    public $identifier;

    public static function getPk()
    {
        return 'identifier';
    }
}
