<?php
/**
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\NcTheming\AppInfo;

use OC\AppFramework\DependencyInjection\DIContainer;
use OCA\NcTheming\Service\NcThemesService;
use OCA\NcTheming\Themes\NCDefaultTheme;
use OCA\Theming\Service\ThemesService;
use OCA\Theming\Themes\DarkHighContrastTheme;
use OCA\Theming\Themes\DarkTheme;
use OCA\Theming\Themes\DefaultTheme;
use OCA\Theming\Themes\DyslexiaFont;
use OCA\Theming\Themes\HighContrastTheme;
use OCA\Theming\Themes\LightTheme;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\QueryException;
use OCP\IConfig;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class Application extends App implements IBootstrap {
	public const APP_ID = 'nc_theming';

	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	/**
	 * Get the container for another app in order to override services
	 * @param string $appName the name of the app to get the container for
	 * @return DIContainer
	 */
	public function getAppContainer(string $appName) {
		try {
			$container = \OC::$server->getRegisteredAppContainer($appName);
		} catch (QueryException) {
			$container = new DIContainer($appName);
			\OC::$server->registerAppContainer($appName, $container);
		}

		return $container;
	}

	public function register(IRegistrationContext $context): void {
		$this->getAppContainer('theming')->registerService(ThemesService::class, function ($c) {
			return new NcThemesService(
				$c->get(IUserSession::class),
				$c->get(IConfig::class),
				$c->get(LoggerInterface::class),
				$c->get(NCDefaultTheme::class),
				$c->get(DefaultTheme::class),   // the rest is overhead due to undefined interface (yet)
				$c->get(LightTheme::class),
				$c->get(DarkTheme::class),
				$c->get(HighContrastTheme::class),
				$c->get(DarkHighContrastTheme::class),
				$c->get(DyslexiaFont::class)
			);
		});
	}

	public function boot(IBootContext $context): void {
	}
}
