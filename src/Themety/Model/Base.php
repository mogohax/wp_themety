<?php

namespace Themety\Model;

use WP_Query;

use Themety\Model\Tools\QueryBulder;
use Themety\Model\Tools\Collection;

abstract class Base {

     protected $modelClass = 'Themety\Model\Tools\PostModel';


     /**
     *  WP_Query instance
     *
     * @var WP_Query
     */
    protected $query;

    /**
     * Post count
     *
     * @var integer
     */
    protected $post_count = false;

    /**
     * Current model's index
     *
     * @var integer
     */
    protected $index = false;

    /**
     * Models
     *
     * @var array
     */
    protected $models = [];


    /**
     * Default query vars
     *
     * @var array
     */
    protected $defaults = array();

    /**
     * Construction
     *
     * @param mixed WP_Query params
     */
    public function __construct($args = null)
    {
        $args && ($this->query($args));
    }


    /**
     * Call QueryBuilder method
     *
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public static function __callStatic($name, $arguments)
    {
        $class = get_called_class();
        $bulder = new QueryBulder(new $class);
        return call_user_func_array([$bulder, $name], $arguments);
    }


    /**
     * Get current model argument
     *
     * @param string $name
     */
    public function __get($name)
    {

    }


    /**
     * Get
     *
     * @param array $args query params
     * @return self
     */
    public static function get(array $args = array())
    {
        $class = get_called_class();
        $model = new $class;
        return $model->query($args);
    }


    /**
     * Create new query
     *
     * @param array $args Query arguments
     * @return self
     */
    public function query(array $args)
    {
        $args = $this->updateQueryVars($args);
        $this->query = new WP_Query($args);

        $items = [];
        foreach($this->query->get_posts() as $post) {
            $items[] = new $this->modelClass($post);
        }

        $collection = new Collection($items);
        return $collection;
    }

    /**
     * Reset query
     */
    public function reset()
    {
        wp_reset_postdata();
    }


    /**
     * Update query vars
     *
     * @param array $args Query vars
     */
    protected function updateQueryVars(array $args)
    {
        isset($args['posts_per_page']) || $args['posts_per_page'] = -1;

        return array_merge($this->defaults, $args);
    }
}
