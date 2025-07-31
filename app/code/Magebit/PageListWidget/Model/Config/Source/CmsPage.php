<?php
/**
 * Copyright © Magebit, LLC. All rights reserved.
 */

declare(strict_types=1);

namespace Magebit\PageListWidget\Model\Config\Source;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * CMS Page source model for widget configuration
 */
class CmsPage implements OptionSourceInterface
{
    /**
     * @var PageRepositoryInterface
     */
    private PageRepositoryInterface $pageRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var array|null
     */
    private ?array $options = null;

    /**
     * CmsPage constructor.
     *
     * @param PageRepositoryInterface $pageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->pageRepository = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Get options array for CMS pages
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];
            
            try {
                $storeId = (int)$this->storeManager->getStore()->getId();
                
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('is_active', 1)
                    ->addFilter('store_id', [0, $storeId], 'in')
                    ->create();

                $pages = $this->pageRepository->getList($searchCriteria);
                
                foreach ($pages->getItems() as $page) {
                    $this->options[] = [
                        'value' => $page->getId(),
                        'label' => $page->getTitle()
                    ];
                }
                
                // Sort by label
                usort($this->options, function ($a, $b) {
                    return strcmp($a['label'], $b['label']);
                });
                
            } catch (\Exception $e) {
                // Log error but don't break the widget
                $this->options = [];
            }
        }

        return $this->options;
    }
} 