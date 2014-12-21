<?php
namespace Sonata\AdminSearchBundle\Tests\DependencyInjection;

use Sonata\AdminSearchBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testValidation()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array('admin_finder_services' => array(
                    'my_admin' => array(
                        'not_finder' => 42
                    )
                ))
            )
        );
    }

    public function testProcessing()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('admin_finder_services' => array(
                    'my_admin' => array(
                        'finder' => 42
                    )
                ))
            ),
            array('admin_finder_services' => array(
                'my_admin' => array(
                    'finder' => 42
                )
            ))
        );
    }
}
