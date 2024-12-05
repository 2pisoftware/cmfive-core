<?php

namespace Html\Cmfive;

use HtmlBootstrap5;

/**
 * UI helper for enabling multiple pagination controls on a page, all dynamic
 * interaction handled by templates/base/src/js/components/TabbedPagination.ts
 */
class TabbedPagination extends \Html\Element
{
    private array $_pages = [];
    private string $_tab = '';

    public function setPages(array $pages): self
    {
        $this->_pages = $pages;
        return $this;
    }

    public function setTab(string $tab): self
    {
        $this->_tab = $tab;
        return $this;
    }

    public function __toString(): string
    {
        $pages = '';
        $page_number = 1;
        foreach ($this->_pages as $page) {
            $hidden = $page_number > 1 ? 'd-none' : '';
            $pages .= "<div class='$hidden' data-page-number='$page_number'>$page</div>";

            $page_number++;
        }

        $controls = HtmlBootstrap5::pagination(1, count($this->_pages), null, null, null, tab: $this->_tab);

        return "<div id='$this->_tab-tabbed-pagination'>
            $pages
            $controls
        </div>";
    }
}
