<?php
/**
 * SPDX-FileLicenseText: 2023 T-Systems International
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NcTheming\Service;

use OCA\Theming\ITheme;
use OCA\Theming\Service\ThemesService;
use OCA\Theming\Themes\DarkHighContrastTheme;
use OCA\Theming\Themes\DarkTheme;
use OCA\Theming\Themes\DefaultTheme;
use OCA\Theming\Themes\DyslexiaFont;
use OCA\Theming\Themes\HighContrastTheme;
use OCA\Theming\Themes\LightTheme;
use OCP\IConfig;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

/**
 * ThemesService to override default themes
 */
class OverrideThemesService extends ThemesService {
	/** @var ITheme[] */
	private array $themeClasses;

	/** @var string[] */
	private array $staticThemeIds;

	/**
	 * Override default theme on service creation
	 *
	 * @param ITheme $defaultOverride The registered default theme that is
	 *                                used as fallback. It is also selectable.
	 */
	public function __construct(
		IUserSession          $userSession,
		IConfig               $config,
		LoggerInterface       $logger,
		ITheme                $defaultOverride,
		DefaultTheme          $defaultTheme,
		LightTheme            $lightTheme,
		DarkTheme             $darkTheme,
		HighContrastTheme     $highContrastTheme,
		DarkHighContrastTheme $darkHighContrastTheme,
		DyslexiaFont          $dyslexiaFont) {
		parent::__construct($userSession, $config, $logger, $defaultTheme, $lightTheme,
			$darkTheme, $highContrastTheme, $darkHighContrastTheme, $dyslexiaFont);

		$this->staticThemeIds = [$defaultOverride->getId()];
		$this->themeClasses = [$defaultOverride->getId() => $defaultOverride];
	}

	/**
	 * Get the overridden list of registered themes
	 *
	 * @return ITheme[]
	 */
	public function getThemes(): array {
		return $this->themeClasses;
	}

	/**
	 * Get the overridden list of all enabled themes IDs
	 * for the logged-in user
	 *
	 * @return string[]
	 */
	public function getEnabledThemes(): array {
		return $this->staticThemeIds;
	}

	/**
	 * Don't enable any themes for the logged-in user
	 *
	 * @param ITheme $theme the theme to enable
	 * @return string[] the enabled themes
	 */
	public function enableTheme(ITheme $theme): array {
		return $this->getEnabledThemes();
	}

	/**
	 * Don't disable a theme for the logged-in user
	 *
	 * @param ITheme $theme the theme to disable
	 * @return string[] the enabled themes
	 */
	public function disableTheme(ITheme $theme): array {
		return $this->getEnabledThemes();
	}

	/**
	 * Only enable the default theme
	 *
	 * @param ITheme $theme the theme to check
	 * @return bool
	 */
	public function isEnabled(ITheme $theme): bool {
		return $theme->getId() === 'default';
	}
}
