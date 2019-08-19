<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class QueryBuilderFactory
{
    /**
     * @var Capsule
     */
    protected $capsule;
    
    /**
     * @var array
     */
    protected $defaultConfig;

    /**
     * @var array
     */
    protected $configuredConnections = [];

    public function __construct($host, $port, $database, $user, $password)
    {
        $this->defaultConfig = [
            'driver' => 'mysql',
            'host'      => $host . ':' . $port,
            'database'  => $database,
            'username'  => $user,
            'password'  => $password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'tl_',
        ];

        $this->capsule = new Capsule();
        $this->capsule->addConnection($this->defaultConfig);
    }

    public function create(array $config = []): QueryBuilder
    {
        if (empty($config)) {
            $connection = $this->capsule->getConnection();
        } else {
            // Store configured connections with their index as connection name
            $index = (string) array_search($config, $this->configuredConnections);

            if (strlen($index) === 0) {
                $index = (string) sizeof($this->configuredConnections);
                $this->configuredConnections[] = $config;

                $this->capsule->addConnection(
                    array_merge($this->defaultConfig, $config),
                    $index
                );
            }

            $connection = $this->capsule->getConnection($index);
        }

        return new QueryBuilder($connection);
    }

    public function getCapsule()
    {
        return $this->capsule;
    }

    public function setCapsule(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }
}
