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

		return array_merge(
			$defaultVariables,
			$ionosVariables,
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
		";
	}
}
