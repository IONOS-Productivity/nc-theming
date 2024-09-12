<?php

declare(strict_types=1);

/**
 * SPDX-FileLicenseText: 2023 T-Systems International
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NcTheming\Tests\AppInfo;

use OC\AppFramework\Bootstrap\Coordinator;
use OCA\NcTheming\AppInfo\Application;
use OCA\NcTheming\Service\OverrideThemesService;
use OCA\Theming\Service\ThemesService;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase {
	private Application $application;

	protected function setUp(): void {
		parent::setUp();

		$this->application = new Application();
		$coordinator = \OC::$server->get(Coordinator::class);
		$this->application->register($coordinator->getRegistrationContext()->for('nc_theming'));
	}

	public function testThemingOverrideRegistration(): void {
		$themesService = \OC::$server->getRegisteredAppContainer('theming')->get(ThemesService::class);
		$this->assertInstanceOf(OverrideThemesService::class, $themesService, 'FATAL: OverrideThemesService is not registered!');
	}
}
