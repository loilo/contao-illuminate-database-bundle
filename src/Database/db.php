<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Database;

/**
 * Get an Illuminate Query Builder for Contao
 *
 * @return QueryBuilder
 */
function db(): QueryBuilder
{
    return \System::getContainer()
        ->get('loilo_contao_illuminate_database.builder_factory')
        ->create();
}
