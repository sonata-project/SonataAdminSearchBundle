<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Sonata\AdminSearchBundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    public function testValidation()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array('admin_finder_services' => array(
                    'my_admin' => array(
                        'not_finder' => 42,
                    ),
                )),
            )
        );
    }

    public function testProcessing()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array('admin_finder_services' => array(
                    'my_admin' => array(
                        'finder' => 42,
                        'actions' => array('list'),
                    ),
                )),
            ),
            array('admin_finder_services' => array(
                'my_admin' => array(
                    'finder' => 42,
                    'actions' => array('list'),
                ),
            ))
        );
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
