<?php

declare(strict_types=1);
/**
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NcTheming\Themes;

use OCA\Theming\ImageManager;
use OCA\Theming\ITheme;
use OCA\Theming\Themes\DefaultTheme;
use OCA\Theming\ThemingDefaults;
use OCA\Theming\Util;
use OCP\App\IAppManager;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserSession;

class NCDefaultTheme extends DefaultTheme implements ITheme {
	public function __construct(
		public Util            $util,
		public ThemingDefaults $themingDefaults,
		public IUserSession    $userSession,
		public IURLGenerator   $urlGenerator,
		public ImageManager    $imageManager,
		public IConfig         $config,
		public IL10N           $l,
		public IAppManager     $appManager,
		private ?IRequest      $request,
	) {
		parent::__construct(
			$util,
			$themingDefaults,
			$userSession,
			$urlGenerator,
			$imageManager,
			$config,
			$l,
			$appManager,
			$request,
		);
	}

	public function getId(): string {
		return 'default';
	}

	public function getTitle(): string {
		return $this->l->t('NC theme');
	}

	public function getEnableLabel(): string {
		return $this->l->t('Enable NC theme');
	}

	public function getDescription(): string {
		return $this->l->t('The NC appearance.');
	}

	public function getMediaQuery(): string {
		return '(prefers-color-scheme: light)';
	}

	public function getMeta(): array {
		// https://html.spec.whatwg.org/multipage/semantics.html#meta-color-scheme
		return [[
			'name' => 'color-scheme',
			'content' => 'light',
		]];
	}

	public function getCSSVariables(): array {
		$defaultVariables = parent::getCSSVariables();
		$originalFontFace = $defaultVariables['--font-face'];

		// IONOS COLORS
		$ionColorMainBackground = '#fff';
		$ionColorPrimary = '#003d8f';
		$ionColorSecondary = '#001b41';
		$ionColorBlueB1 = '#dbedf8';
		$ionColorBlueB2 = '#95caeb';
		$ionColorBlueB4 = '#1474c4';
		$ionColorCoolGrayC1 = '#f4f7fa';
		$ionColorCoolGrayC2 = '#dbe2e8';
		$ionColorCoolGrayC3 = '#bcc8d4';
		$ionColorCoolGrayC5 = '#718095';
		$ionColorTypoMild = '#2e4360';
		$ionColorLightGray = '#d7d7d7';

		$ionColorGreenG3 = '#12cf76';
		$ionColorRoseR3 = '#ff6159';
		$ionColorSkyS3 = '#11c7e6';
		$ionColorAmberY3 = '#ffaa00';

		$ionosVariables = [
			'--ion-color-main-background' => $ionColorMainBackground,
			'--ion-color-primary' => $ionColorPrimary,
			'--ion-color-secondary' => $ionColorSecondary,
			'--ion-color-blue-b1' => $ionColorBlueB1,
			'--ion-color-blue-b2' => $ionColorBlueB2,
			'--ion-color-blue-b4' => $ionColorBlueB4,
			'--ion-color-cool-gray-c1' => $ionColorCoolGrayC1,
			'--ion-color-cool-gray-c2' => $ionColorCoolGrayC2,
			'--ion-color-cool-gray-c3' => $ionColorCoolGrayC3,
			'--ion-color-cool-gray-c5' => $ionColorCoolGrayC5,
			'--ion-color-typo-mild' => $ionColorTypoMild,
			'--ion-color-light-gray' => $ionColorLightGray,
			'--ion-color-green-g3' => $ionColorGreenG3,
			'--ion-color-rose-r3' => $ionColorRoseR3,
			'--ion-color-sky-s3' => $ionColorSkyS3,
			'--ion-color-amber-y3' => $ionColorAmberY3,
		];

		// COLOR MAPPING
		$colorMainText = $ionColorTypoMild;
		$colorMainTextRgb = join(',', $this->util->hexToRGB($colorMainText));
		$colorTextMaxcontrast = $ionColorTypoMild;
		

		$colorMainBackground = '#fff';
		$colorMainBackgroundRGB = join(',', $this->util->hexToRGB($colorMainBackground));
		$colorBoxShadow = $this->util->darken($colorMainBackground, 70);
		$colorBoxShadowRGB = join(',', $this->util->hexToRGB($colorBoxShadow));

		$colorError = $ionColorRoseR3;
		$colorWarning = $ionColorAmberY3;
		$colorSuccess = $ionColorGreenG3;
		$colorInfo = $ionColorSkyS3;
	
		$variables = [
			'--color-main-background' => $colorMainBackground,
			'--color-main-background-rgb' => $colorMainBackgroundRGB,
			'--color-main-background-translucent' => 'rgba(var(--color-main-background-rgb), .97)',
			'--color-main-background-blur' => 'rgba(var(--color-main-background-rgb), .8)',

			// to use like this: background-image: linear-gradient(0, var('--gradient-main-background));
			'--gradient-main-background' => 'var(--color-main-background) 0%, var(--color-main-background-translucent) 85%, transparent 100%',

			// used for different active/hover/focus/disabled states
			'--color-background-hover' => 'rgba(248, 248, 248, 1)',
			'--color-background-dark' => $this->util->darken($colorMainBackground, 7),
			'--color-background-darker' => $this->util->darken($colorMainBackground, 14),

			'--color-placeholder-light' => $this->util->darken($colorMainBackground, 10),
			'--color-placeholder-dark' => $this->util->darken($colorMainBackground, 20),

			// max contrast for WCAG compliance
			'--color-main-text' => $colorMainText,
			'--color-text-maxcontrast' => $colorTextMaxcontrast,
			'--color-text-maxcontrast-default' => $colorTextMaxcontrast,
			'--color-text-maxcontrast-background-blur' => $this->util->darken($colorTextMaxcontrast, 7),
			'--color-text-light' => 'var(--color-main-text)', // deprecated
			'--color-text-lighter' => 'var(--color-text-maxcontrast)', // deprecated

			'--color-scrollbar' => 'rgba(' . $colorMainTextRgb . ', .15)',

			// error/warning/success/info feedback colours
			'--color-error' => $colorError,
			'--color-error-rgb' => join(',', $this->util->hexToRGB($colorError)),
			'--color-error-hover' => $this->util->mix($colorError, $colorMainBackground, 75),
			'--color-error-text' => $this->util->darken($colorError, 5),
			'--color-warning' => $colorWarning,
			'--color-warning-rgb' => join(',', $this->util->hexToRGB($colorWarning)),
			'--color-warning-hover' => $this->util->darken($colorWarning, 5),
			'--color-warning-text' => $this->util->darken($colorWarning, 7),
			'--color-success' => $colorSuccess,
			'--color-success-rgb' => join(',', $this->util->hexToRGB($colorSuccess)),
			'--color-success-hover' => $this->util->mix($colorSuccess, $colorMainBackground, 80),
			'--color-success-text' => $this->util->darken($colorSuccess, 4),
			'--color-info' => $colorInfo,
			'--color-info-rgb' => join(',', $this->util->hexToRGB($colorInfo)),
			'--color-info-hover' => $this->util->mix($colorInfo, $colorMainBackground, 80),
			'--color-info-text' => $this->util->darken($colorInfo, 4),
			'--color-favorite' => $ionColorAmberY3,
			// used for the icon loading animation
			'--color-loading-light' => '#cccccc',
			'--color-loading-dark' => '#444444',

			'--color-box-shadow-rgb' => $colorBoxShadowRGB,
			'--color-box-shadow' => "rgba(var(--color-box-shadow-rgb), 0.5)",

			'--color-border' => $this->util->darken($colorMainBackground, 7),
			'--color-border-dark' => $this->util->darken($colorMainBackground, 14),
			'--color-border-maxcontrast' => $this->util->darken($colorMainBackground, 51),

			'--default-font-size' => '15px',

			// TODO: support "(prefers-reduced-motion)"
			'--animation-quick' => '100ms',
			'--animation-slow' => '300ms',

			// Default variables --------------------------------------------
			// Border width for input elements such as text fields and selects
			'--border-width-input' => '1px',
			'--border-width-input-focused' => '2px',
			'--border-radius' => '3px',
			'--border-radius-large' => '10px',
			'--border-radius-rounded' => '28px',
			// pill-style button, value is large so big buttons also have correct roundness
			'--border-radius-pill' => '100px',

			'--default-clickable-area' => '44px',
			'--default-line-height' => '24px',
			'--default-grid-baseline' => '4px',

			// various structure data
			'--header-height' => '50px',
			'--navigation-width' => '300px',
			'--sidebar-min-width' => '300px',
			'--sidebar-max-width' => '500px',
			'--list-min-width' => '200px',
			'--list-max-width' => '300px',
			'--header-menu-item-height' => '44px',
			'--header-menu-profile-item-height' => '66px',

			// mobile. Keep in sync with core/js/js.js
			'--breakpoint-mobile' => '1024px',
			'--background-invert-if-dark' => 'no',
			'--background-invert-if-bright' => 'invert(100%)',
			'--background-image-invert-if-bright' => 'no',
			'--background-image-color-text' => '#ffffff',
		];

		return array_merge(
			$defaultVariables,
			$ionosVariables,
			$variables,
			[
				'--font-face' => '"Open sans", ' . $originalFontFace
			]
		);
	}

	public function getCustomCss(): string {
		$regularEot = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Regular-webfont.eot');
		$regularWoff = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Regular-webfont.woff');
		$regularWoff2 = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Regular-webfont.woff2');
		$regularTtf = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Regular-webfont.ttf');
		$regularSvg = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Regular-webfont.svg#open_sansregular');

		$semiBoldEot = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-SemiBold-webfont.eot');
		$semiBoldWoff = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-SemiBold-webfont.woff');
		$semiBoldWoff2 = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-SemiBold-webfont.woff2');
		$semiBoldTtf = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-SemiBold-webfont.ttf');
		$semiBoldSvg = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-SemiBold-webfont.svg#open_sansregular');

		$boldEot = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Bold-webfont.eot');
		$boldWoff = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Bold-webfont.woff');
		$boldWoff2 = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Bold-webfont.woff2');
		$boldTtf = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Bold-webfont.ttf');
		$boldSvg = $this->urlGenerator->linkTo('nc_theming', 'fonts/OpenSans/OpenSans-Bold-webfont.svg#open_sansregular');

		return "
		@font-face {
			font-family: 'Open sans';
			src: url('$regularEot') format('embedded-opentype'),
				url('$regularWoff') format('woff'),
				url('$regularWoff2') format('woff2'),
				url('$regularTtf') format('truetype'),
				url('$regularSvg') format('svg');
			font-weight: normal;
			font-style: normal;
			font-display: swap;
		}

		/* Open sans semi-bold variant */
		@font-face {
			font-family: 'Open sans';
			src: url('$semiBoldEot') format('embedded-opentype'),
				url('$semiBoldWoff') format('woff'),
				url($semiBoldWoff2) format('woff2'),
				url('$semiBoldTtf') format('truetype'),
				url('$semiBoldSvg') format('svg');
			font-weight: 600;
			font-style: normal;
			font-display: swap;
		}

		/* Open sans bold variant */
		@font-face {
			font-family: 'Open sans';
			src: url('$boldEot') format('embedded-opentype'),
				url('$boldWoff') format('woff'),
				url('$boldWoff2') format('woff2'),
				url('$boldTtf') format('truetype'),
				url('$boldSvg') format('svg');
			font-weight: bold;
			font-style: normal;
			font-display: swap;
		}

		[data-cy-files-navigation].app-navigation {
			background-color: var(--ion-color-cool-grey-c1) !important;

			.app-navigation-entry {
				&.active[data-v-c00d5366] {
					background-color: var(--ion-color-cool-grey-c3) !important;

					.app-navigation-entry-link[data-v-c00d5366] {
						color: var(--ion-color-secondary) !important;
					}

					&:hover {
						background-color: var(--ion-color-cool-grey-c3) !important;
					}
				}
				&:hover {
					background-color: var(--ion-color-cool-grey-c2) !important;
				}
			}
			
			.app-navigation-entry--opened {
				background: var(--ion-color-cool-grey-c3) !important;

				.app-navigation-entry__children {
					.app-navigation-entry-wrapper {
						background: var(--ion-color-main-background) !important;
					}
				}
			}
		}

		.files-list[data-v-1e2b43b6] {
			.files-list__row {
				&:hover {
					background-color: var(--ion-color-cool-grey-c2) !important;
				}

				&.active {
					background-color: var(--ion-color-cool-grey-c2) !important;
				}

				.files-list__row-icon {
					color: var(--ion-color-blue-b4) !important;
				}

				.files-list__row-checkbox  {
					color: red;
				}

				.files-list__row-checkbox .checkbox-radio-switch__icon {
					.material-design-icon .checkbox-blank-outline-icon {
						color: var(--ion-color-cool-grey-c5) !important;
					}

					.material-design-icon.checkbox-marked-icon {
						color: var(--ion-color-blue-b4) !important;
					}
					.material-design-icon minus-box-icon {
						color: var(--ion-color-blue-b4) !important;
					}
				}
			}
		}
		";
	}
}
