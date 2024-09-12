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

class OverrideDefaultTheme extends DefaultTheme implements ITheme {
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

	public function getTitle(): string {
		return 'NC default theme';
	}

	public function getEnableLabel(): string {
		return 'Enable the NC default theme';
	}

	public function getDescription(): string {
		return 'The default NC appearance.';
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

		return array_merge(
			$defaultVariables,
			[
			]
		);
	}
}
