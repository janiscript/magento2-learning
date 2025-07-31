<?php
/**
 * Copyright © Magebit, LLC. All rights reserved.
 */

declare(strict_types=1);

namespace Magebit\PageListWidget\Block\Widget;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;

/**
 * CMS Page List Widget Block
 */
class PageList extends Template implements BlockInterface
{
    /**
     * Widget option constants
     */
    public const OPTION_TITLE = 'title';
    public const OPTION_DISPLAY_MODE = 'display_mode';
    public const OPTION_SELECTED_PAGES = 'selected_pages';

    /**
     * Display mode constants
     */
    public const DISPLAY_MODE_ALL_PAGES = 'all_pages';
    public const DISPLAY_MODE_SPECIFIC_PAGES = 'specific_pages';

    /**
     * @var string
     */
    protected $_template = 'Magebit_PageListWidget::widget/page_list.phtml';

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
    private ?array $cmsPages = null;

    /**
     * PageList constructor.
     *
     * @param Template\Context $context
     * @param PageRepositoryInterface $pageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->pageRepository = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Get widget title
     *
     * @return string
     */
    public function getWidgetTitle(): string
    {
        return (string)$this->getData(self::OPTION_TITLE);
    }

    /**
     * Get display mode
     *
     * @return string
     */
    public function getDisplayMode(): string
    {
        return $this->getData(self::OPTION_DISPLAY_MODE) ?: self::DISPLAY_MODE_ALL_PAGES;
    }

    /**
     * Get selected page IDs
     *
     * @return array
     */
    public function getSelectedPageIds(): array
    {
        $selectedPages = $this->getData(self::OPTION_SELECTED_PAGES);
        
        if (empty($selectedPages)) {
            return [];
        }

        // Handle both string and array formats
        if (is_string($selectedPages)) {
            return array_filter(explode(',', $selectedPages));
        }

        return is_array($selectedPages) ? $selectedPages : [];
    }

    /**
     * Get CMS pages based on widget configuration
     *
     * @return PageInterface[]
     */
    public function getCmsPages(): array
    {
        if ($this->cmsPages === null) {
            $this->cmsPages = $this->loadCmsPages();
        }

        return $this->cmsPages;
    }

    /**
     * Load CMS pages based on display mode
     *
     * @return PageInterface[]
     */
    private function loadCmsPages(): array
    {
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $searchCriteriaBuilder = $this->searchCriteriaBuilder
                ->addFilter('is_active', 1)
                ->addFilter('store_id', [0, $storeId], 'in');

            // Filter by specific pages if needed
            if ($this->getDisplayMode() === self::DISPLAY_MODE_SPECIFIC_PAGES) {
                $selectedPageIds = $this->getSelectedPageIds();
                if (empty($selectedPageIds)) {
                    return [];
                }
                $searchCriteriaBuilder->addFilter('page_id', $selectedPageIds, 'in');
            }

            $searchCriteria = $searchCriteriaBuilder->create();
            $pages = $this->pageRepository->getList($searchCriteria);

            return $pages->getItems();
            
        } catch (\Exception $e) {
            $this->_logger->error('Error loading CMS pages for widget: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get page URL
     *
     * @param PageInterface $page
     * @return string
     */
    public function getPageUrl(PageInterface $page): string
    {
        return $this->getUrl($page->getIdentifier());
    }

    /**
     * Check if widget has title
     *
     * @return bool
     */
    public function hasTitle(): bool
    {
        return !empty($this->getWidgetTitle());
    }

    /**
     * Check if widget has pages to display
     *
     * @return bool
     */
    public function hasPages(): bool
    {
        return !empty($this->getCmsPages());
    }

    /**
     * Get cache key information
     *
     * @return array
     */
    public function getCacheKeyInfo(): array
    {
        return array_merge(parent::getCacheKeyInfo(), [
            $this->getDisplayMode(),
            implode(',', $this->getSelectedPageIds()),
            $this->storeManager->getStore()->getId()
        ]);
    }
} 