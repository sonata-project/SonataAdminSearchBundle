<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sonata\AdminSearchBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testValidation(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                ['admin_finder_services' => [
                    'my_admin' => [
                        'not_finder' => 42,
                    ],
                ]],
            ]
        );
    }

    public function testProcessing(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['admin_finder_services' => [
                    'my_admin' => [
                        'finder' => 42,
                        'actions' => ['list'],
                    ],
                ]],
            ],
            ['admin_finder_services' => [
                'my_admin' => [
                    'finder' => 42,
                    'actions' => ['list'],
                ],
            ]]
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
