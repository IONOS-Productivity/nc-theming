<?php
/**
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NcTheming\Service;

use OCA\NcTheming\AppInfo\Application;
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
class NcThemesService extends ThemesService {
	private IUserSession $userSession;
	private IConfig $config;

	/** @var ITheme[] */
	private array $themeClasses;

	/** @var string[] */
	private array $staticThemeIds;

	/** @var string[] */
	private array $selectableThemeIds;

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

		$this->userSession = $userSession;
		$this->config = $config;

		$this->staticThemeIds = [ $defaultOverride->getId() ];
		$this->themeClasses = [ $defaultOverride->getId() => $defaultOverride ];
		$this->selectableThemeIds = [
			$defaultOverride->getId()
		];
	}

	/**
	 * Get the list of all registered themes
	 *
	 * @return ITheme[] the (id, $theme) map of registered themes
	 */
	public function getThemes(): array {
		return $this->themeClasses;
	}

	/**
	 * Get the list of all enabled themes IDs
	 * for the logged-in user
	 *
	 * @return string[]
	 */
	public function getEnabledThemes(): array {
		$enforcedThemeId = $this->config->getSystemValueString('enforce_theme', '');
		if ($enforcedThemeId !== '') {
			// a theme enforce page setup, make sure that the theme is in selectable list
			return array_unique(array_merge([$enforcedThemeId], $this->staticThemeIds));
		}

		$user = $this->userSession->getUser();
		if ($user === null) {
			// a non-enforce, anonymous page setup
			return array_unique(array_merge(['default'], $this->staticThemeIds));
		}

		// a user selected page setup
		$userValues = $this->config->getUserValue($user->getUID(), Application::APP_ID, 'enabled-themes', '[]');
		$enabledThemeIds = json_decode($userValues);
		$selectedThemeIds = array_intersect($this->selectableThemeIds, $enabledThemeIds);
		if (empty($selectedThemeIds)) {
			// add default to the enabled fonts adn statics (as there is no selected theme)
			return array_unique(array_merge(['default'], $this->staticThemeIds));
		}

		return array_unique(array_merge($selectedThemeIds, $this->staticThemeIds));
	}

	/**
	 * Enable a theme for the logged-in user
	 *
	 * @param ITheme $theme the theme to enable
	 * @return string[] the enabled themes
	 */
	public function enableTheme(ITheme $theme): array {
		$themeIds = $this->getEnabledThemes();

		// If already enabled, ignore
		if (in_array($theme->getId(), $themeIds)) {
			return $themeIds;
		}

		// we are not in user context
		$user = $this->userSession->getUser();
		if ($user === null) {
			return $themeIds;
		}

		if (in_array($theme->getId(), $this->selectableThemeIds)) {
			// change selection and sort it
			$themeIds = array_unique(
				array_merge([$theme->getId()],
					array_diff($themeIds, $this->selectableThemeIds)));
		} // statics are already always included

		$this->config->setUserValue($user->getUID(), Application::APP_ID,
			'enabled-themes', json_encode(array_values($themeIds)));
		return $themeIds;
	}

	/**
	 * Disable a theme for the logged-in user
	 *
	 * @param ITheme $theme the theme to disable
	 * @return string[] the enabled themes
	 */
	public function disableTheme(ITheme $theme): array {
		$themeIds = $this->getEnabledThemes();

		$foundId = array_search($theme->getId(), $themeIds);

		// If already disabled, ignore
		if (!$foundId) {
			return $themeIds;
		}

		if (in_array($theme->getId(), $this->staticThemeIds)) {
			return $themeIds;
		}

		// we are not in user context
		$user = $this->userSession->getUser();
		if ($user === null) {
			return $themeIds;
		}

		// we are not testing for not disabling a selectable theme as this
		// may produces trouble with Nextcloud settings page (TODO)

		array_splice($themeIds, $foundId, $foundId);
		$this->config->setUserValue($user->getUID(), Application::APP_ID,
			'enabled-themes', json_encode(array_values($themeIds)));
		return $themeIds;
	}

	/**
	 * Check whether a theme is enabled or not
	 *
	 * @return bool
	 */
	public function isEnabled(ITheme $theme): bool {
		$themeIds = $this->getEnabledThemes();
		return in_array($theme->getId(), $themeIds);
	}

}
