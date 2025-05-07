<?php

/**
 * SPDX-FileLicenseText: 2023 T-Systems International
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\NcTheming\AppInfo;

use OC\AppFramework\Bootstrap\Coordinator;
use OC\AppFramework\DependencyInjection\DIContainer;
use OC\Search\SearchComposer;
use OCA\NcTheming\Service\OverrideThemesService;
use OCA\NcTheming\Themes\OverrideDefaultTheme;
use OCA\NcTheming\Search\SearchComposerDecorator;
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
use OCP\IURLGenerator;
use OCP\IUserSession;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class Application extends App implements IBootstrap {
	public const APP_ID = 'nc_theming';

	public const ALLOWED_SEARCH_PROVIDERS = [
		'files',
	];

	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	/**
	 * Get the container for the "theming" core app
	 * @return DIContainer
	 */
	private function getCoreThemingAppContainer(): DIContainer {
		$appName = 'theming';

		// Apps are registered in an order controlled by the server. As of date
		// of authoring the order is alphabetical. This means the core "theming"
		// might get registered after our theming app.
		// Here we handle both cases, eventually registering the "theming"
		// ourselves in case it could not be looked up.
		try {
			$container = \OC::$server->getRegisteredAppContainer($appName);
		} catch (QueryException) {
			$container = new DIContainer($appName);
		}

		return $container;
	}

	public function register(IRegistrationContext $context): void {
		// Replace service registration for the ThemeService by our wrapper
		// Inspired by https://github.com/nextmcloud/nmctheme/
		$this->getCoreThemingAppContainer()->registerService(ThemesService::class, function ($c) {
			return new OverrideThemesService(
				$c->get(IUserSession::class),
				$c->get(IConfig::class),
				$c->get(LoggerInterface::class),
				$c->get(OverrideDefaultTheme::class),
				$c->get(DefaultTheme::class),
				$c->get(LightTheme::class),
				$c->get(DarkTheme::class),
				$c->get(HighContrastTheme::class),
				$c->get(DarkHighContrastTheme::class),
				$c->get(DyslexiaFont::class)
			);
		});

		// allow only desired search providers for full-text search
		$this->registerSearchComposerDecorator($context);
	}

	public function boot(IBootContext $context): void {
	}

	/**
	 * Decorate SearchComposer with allowed search providers -
	 * to ensure being listed and used for searches
	 *
	 * For allow list, the ids of the Search\IProvider is used
	 */
	protected function registerSearchComposerDecorator(IRegistrationContext $context) {
		$this->getContainer()->getServer()->registerService(SearchComposer::class,
			function ($c) {
				return new SearchComposerDecorator(
					new SearchComposer(
						$c->get(Coordinator::class),
						$c->get(ContainerInterface::class),
						$c->get(IURLGenerator::class),
						$c->get(LoggerInterface::class)
					),
					self::ALLOWED_SEARCH_PROVIDERS
				);
			});
	}
}
