<?php namespace Loilo\ContaoIlluminateDatabaseBundle\Database;

use Contao\Model;
use Illuminate\Database\Query\Builder;

class QueryBuilder extends Builder
{
    /**
     * @var bool
     */
    private $fetchModel = false;

    /**
     * Tell the builder to fetch a Contao model instead of a Laravel collection
     *
     * @return $this
     */
    public function asModel()
    {
        $this->fetchModel = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($columns = ['*'])
    {
        if ($this->fetchModel) {
            $class = Model::getClassFromTable("tl_$this->from");
            $pkField = $class::getPk();

            $this->columns = [ $pkField ];

            return parent::get([ $pkField ])
                ->map(function ($row) use ($class, $pkField) {
                    return $class::findByPk($row->$pkField);
                });
        } else {
            return parent::get($columns);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cursor()
    {
        if ($this->fetchModel) {
            $class = Model::getClassFromTable("tl_$this->from");
            $pkField = $class::getPk();

            $this->columns = [ $pkField ];

            $generator = parent::cursor();

            foreach ($generator as $row) {
                yield $class::findByPk($row->$pkField);
            }
        } else {
            $generator = parent::cursor();

            foreach ($generator as $row) {
                yield $row;
            }
        }
    }
}
