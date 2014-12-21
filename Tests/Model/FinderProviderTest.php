<?php
namespace Sonata\AdminSearchBundle\Tests\Model;

use Sonata\AdminSearchBundle\Model\FinderProvider;

class FinderProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFinderByAdmin()
    {
        $admin     = $this->getMock('Sonata\AdminBundle\Admin\AdminInterface');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $provider  = new FinderProvider(
            $container,
            array(
                $adminId = 'planet_admin' => array(
                    'finder' => $finderId = 'fos_elastica.planet_finder'
                )
            )
        );
        $finder = new \StdClass();

        $container->expects($this->once())
            ->method('get')
            ->with($finderId)
            ->will($this->returnValue($finder));

        $admin->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($adminId));

        $this->assertSame(
            $finder,
            $provider->getFinderByAdmin($admin)
        );
    }
}
