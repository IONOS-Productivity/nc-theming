<?php

declare(strict_types=1);
/**
 * SPDX-FileLicenseText: 2023 T-Systems International
 * SPDX-FileLicenseText: 2024 STRATO AG
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\NcTheming\Search;

use OC\Search\FilterCollection;
use OC\Search\SearchComposer;
use OCP\IUser;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;

/**
 * Allow search types like apps or settings for full-text search
 * so that only these types are delivered to client. Also when using
 * API directly.
 */
class SearchComposerDecorator extends SearchComposer {
	protected SearchComposer $decorated;
	protected array $allowedProviderIds = [];

	public function __construct(
		SearchComposer $decorated,
		array $allowedProviderIds
	) {
		$this->decorated = $decorated;
		$this->allowedProviderIds = $allowedProviderIds;
	}

	/**
	 * Get providers from allowed provider list
	 */
	public function getProviders(string $route, array $routeParameters): array {
		$providers = $this->decorated->getProviders($route, $routeParameters);

		if (empty($this->allowedProviderIds)) {
			return $providers;
		}

		return array_values(array_filter($providers, function ($p) {
			return in_array($p['id'], $this->allowedProviderIds);
		}));
	}

	/**
	 * No decoration, only delegate.
	 */
	public function search(IUser $user, string $providerId, ISearchQuery $query): SearchResult {
		return $this->decorated->search($user, $providerId, $query);
	}

	/**
	 * No decoration, only delegate.
	 */
	public function buildFilterList(string $providerId, array $parameters): FilterCollection {
		return $this->decorated->buildFilterList($providerId, $parameters);
	}
}
