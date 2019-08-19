<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Database;

/**
 * Get an Illuminate Query Builder for Contao
 *
 * @param array $config A connection configuration to use
 * @return QueryBuilder
 */
function db(array $config = []): QueryBuilder
{
    return \System::getContainer()
        ->get('loilo_contao_illuminate_database.builder_factory')
        ->create($config);
}
