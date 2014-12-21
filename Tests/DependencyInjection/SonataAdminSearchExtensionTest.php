<?php
namespace Sonata\AdminSearchBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\AdminSearchBundle\DependencyInjection\SonataAdminSearchExtension;

class SonataAdminSearchExtensionTest extends AbstractExtensionTestCase
{
    public function getContainerExtensions()
    {
        return array(
            new SonataAdminSearchExtension()
        );
    }

    public function testLoad()
    {
        $this->load(array(
            'admin_finder_services' => $expectedParameterValue = array(
                'my_id' => array(
                    'finder' => 'test'
                )
            )
        ));
        $this->assertContainerBuilderHasParameter(
            'sonata.admin.search.admin_finder_services',
            $expectedParameterValue
        );

        $this->assertContainerBuilderHasService(
            'sonata.admin.search.finder_provider'
        );
    }
}
