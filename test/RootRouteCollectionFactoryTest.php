<?php
/**
 * Dash
 *
 * @link      http://github.com/DASPRiD/Dash For the canonical source repository
 * @copyright 2013-2015 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace DashTest;

use Dash\RootRouteCollectionFactory;
use Dash\Route\RouteManager;
use Dash\RouteCollection\LazyRouteCollection;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Dash\RootRouteCollectionFactory
 */
class RootRouteCollectionFactoryTest extends TestCase
{
    public function testFactorySucceedsWithoutConfig()
    {
        $factory    = new RootRouteCollectionFactory();
        $collection = $factory($this->getContainer(), '');

        $this->assertInstanceOf(LazyRouteCollection::class, $collection);
    }

    public function testFactorySucceedsWithEmptyConfig()
    {
        $factory    = new RootRouteCollectionFactory();
        $collection = $factory($this->getContainer([]), '');

        $this->assertInstanceOf(LazyRouteCollection::class, $collection);
    }

    public function testFactoryWithConfig()
    {
        $factory = new RootRouteCollectionFactory();
        $collection = $factory($this->getContainer([
            'dash' => [
                'routes' => [
                    'foo' => ['/bar'],
                ],
            ],
        ]), '');

        $this->assertInstanceOf(LazyRouteCollection::class, $collection);
        $this->assertAttributeSame([
            'foo' => [
                'priority' => 1,
                'serial' => 1,
                'options' => ['/bar'],
                'instance' => null,
            ],
        ], 'routes', $collection);
    }

    /**
     * @param  array $config
     * @return ContainerInterface
     */
    protected function getContainer(array $config = null)
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(RouteManager::class)->willReturn($this->prophesize(RouteManager::class)->reveal());

        if (null !== $config) {
            $container->get('config')->willReturn($config);
            $container->has('config')->willReturn(true);
        } else {
            $container->has('config')->willReturn(false);
        }

        return $container->reveal();
    }
}
