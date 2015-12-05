<?php
/**
 * User: kit
 * Date: 05/12/15
 * Time: 9:05 PM
 */

namespace AppBundle\Services;
use Symfony\Component\DependencyInjection\Container;

class BaseService {
    private $container;

    public function __construct(Container $container){
        $this->setContainer($container);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}