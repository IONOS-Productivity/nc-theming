<?php

declare(strict_types=1);

/**
 * SPDX-FileLicenseText: 2023 T-Systems International
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NcTheming\Tests\Search;

use OC\AppFramework\Bootstrap\Coordinator;
use OC\Search\SearchComposer;
use OCA\NcTheming\AppInfo\Application;
use OCA\NcTheming\Search\SearchComposerDecorator;
use OCP\Collaboration\Collaborators\ISearchResult;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


class SearchComposerDecoratorTest extends TestCase {

	protected \OCP\AppFramework\App $app;
	protected IUser|\PHPUnit\Framework\MockObject\MockObject $user;
	protected ISearchQuery|\PHPUnit\Framework\MockObject\MockObject $query;
	protected SearchComposer|\PHPUnit\Framework\MockObject\MockObject $decoratedSearchComposer;
	protected SearchComposerDecorator $composerService;

	protected function setUp(): void {
		parent::setUp();

		// test behavior in combination with the core apps
		$this->app = new \OCP\AppFramework\App(Application::APP_ID);
	}

	public function testGetProvidersDefault() {
		$this->composerService =
			new SearchComposerDecorator(
				new SearchComposer(
					$this->app->getContainer()->get(Coordinator::class),
					$this->app->getContainer()->get(ContainerInterface::class),
					$this->app->getContainer()->get(IURLGenerator::class),
					$this->app->getContainer()->get(LoggerInterface::class)
				),
				[
				]
			);
		$providers = array_values($this->composerService->getProviders('/', []));
		$providerIds = array_map(function ($p) {
			return $p['id'];
		}, $providers);

		$this->assertContains('files', $providerIds);
		$this->assertContains('systemtags', $providerIds);
		$this->assertContains('comments', $providerIds);
		$this->assertContains('settings_apps', $providerIds);
		$this->assertContains('settings', $providerIds);
	}

	public function testGetProvidersAllowed() {
		$this->composerService =
			new SearchComposerDecorator(
				new SearchComposer(
					$this->app->getContainer()->get(Coordinator::class),
					$this->app->getContainer()->get(ContainerInterface::class),
					$this->app->getContainer()->get(IURLGenerator::class),
					$this->app->getContainer()->get(LoggerInterface::class)
				),
				[
					'files',
					'settings',
				]
			);
		$providers = array_values($this->composerService->getProviders('/', []));
		$providerIds = array_map(function ($p) {
			return $p['id'];
		}, $providers);
		$this->assertContains('settings', $providerIds);
		$this->assertContains('files', $providerIds);
		$this->assertNotContains('settings_apps', $providerIds);
		$this->assertNotContains('systemtags', $providerIds);
	}

	protected function createSearchMock(): void {
		$this->decoratedSearchComposer = $this->createMock(SearchComposer::class);
		$this->composerService =
			new SearchComposerDecorator(
				$this->decoratedSearchComposer,
				[
					'files',
					'settings',
				]
			);

		$this->user = $this->createMock(IUser::class);
		$this->query = $this->createMock(ISearchQuery::class);
	}

	public function testSearchDisabledProvider() {
		$this->createSearchMock();
		/** @var ISearchResult $searchResult */
		$searchResult = SearchResult::complete('files', []);
		$this->decoratedSearchComposer->expects(self::once())
			->method('search')
			->with($this->user, 'files', $this->query)
			->willReturn($searchResult);

		$result = $this->composerService->search($this->user, 'files', $this->query)->jsonSerialize();

		$this->assertEquals('files', $result['name']);
		$this->assertFalse($result['isPaginated']);
		$this->assertEmpty($result['entries']);
	}

	public function testSearch() {
		$this->createSearchMock();
		$this->decoratedSearchComposer->expects(self::once())
			->method("search")
			->with($this->equalTo($this->user), $this->equalTo('files'), $this->equalTo($this->query))
			->willReturn(SearchResult::complete('files', []));
		$this->composerService->search($this->user, 'files', $this->query)->jsonSerialize();
	}
}
