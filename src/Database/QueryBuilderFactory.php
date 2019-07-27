<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class QueryBuilderFactory
{
    protected $connection;

    public function __construct($host, $port, $database, $user, $password)
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver' => 'mysql',
            'host'      => $host . ':' . $port,
            'database'  => $database,
            'username'  => $user,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'tl_',
        ]);

        $this->connection = $capsule->getConnection('default');
    }

    public function create(): QueryBuilder
    {
        return new QueryBuilder($this->connection);
    }
}
