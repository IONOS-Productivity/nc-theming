<?php

declare(strict_types=1);

/**
 * SPDX-FileLicenseText: 2023 T-Systems International
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NcTheming\Tests\AppInfo;

use OC\AppFramework\Bootstrap\Coordinator;
use OC\AppFramework\DependencyInjection\DIContainer;
use OCA\NcTheming\AppInfo\Application;
use OCA\NcTheming\Service\OverrideThemesService;
use OCA\Theming\Service\ThemesService;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase {
	private Application $app;

	private IRegistrationContext $context;

	protected function setUp(): void {
		parent::setUp();

		$this->app = new Application();
		$coordinator = \OC::$server->get(Coordinator::class);
		$this->app->register($coordinator->getRegistrationContext()->for('nc_theming'));

		$this->context = $this->createMock(IRegistrationContext::class);
	}

	public function testThemingOverrideRegistration(): void {
		$themesService = \OC::$server->getRegisteredAppContainer('theming')->get(ThemesService::class);
		$this->assertInstanceOf(OverrideThemesService::class, $themesService, 'FATAL: OverrideThemesService is not registered!');
	}

	public function testRegister(): void {
		$mockDIContainer = $this->getMockBuilder(DIContainer::class)
			->setConstructorArgs(['test'])
			->onlyMethods(['registerService'])
			->getMock();

		$serverMock = $this->getMockBuilder(\OC\Server::class)
			->disableOriginalConstructor()
			->getMock();

		\OC::$server = $serverMock;

		$serverMock->expects($this->exactly(1))
			->method('getRegisteredAppContainer')
			->with('theming')
			->willReturn($mockDIContainer);

		$mockDIContainer->expects($this->exactly(1))
			->method('registerService')
			->with(
				ThemesService::class,
				$this->isInstanceOf(\Closure::class),
				true
			);

		$this->app->register($this->context);
	}
}
