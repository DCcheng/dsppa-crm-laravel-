<?php
/**
 *  FileName: Pager.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/24
 *  Time: 11:34
 */


namespace App\Api\Utils;


class Pager
{
    private $pageParam = 'page';
    private $page = 1;
    private $pageCount = 1;
    public $maxButtonCount = 5;
    public $prevPageLabel = "<";
    public $nextPageLabel = ">";
    public $firstPageLabel = "";
    public $lastPageLabel = "";
    public $activePageCssClass = "btn btn-default active";
    public $internalPageCssClass = "btn btn-default";
    public $morePageCssClass = "btn btn-default";
    public $prevPageCssClass = "btn btn-default";
    public $nextPageCssClass = "btn btn-default";
    public $disabledPageCssClass = "btn btn-default disable";

    public function __construct($total, $size)
    {
        $this->page = request()->get($this->pageParam);
        $this->pageCount = ceil($total / $size);
    }

    public static function init($total, $size)
    {
        $page = new Pager($total, $size);
        return $page;
    }

    public static function create($total, $size)
    {
        $page = self::init($total, $size);
        return [$page->renderPageButtons(),$page->getPageCount()];
    }

    protected function getPage()
    {
        return (int)$this->page;
    }

    protected function getPageCount()
    {
        return (int)$this->pageCount;
    }


    /**
     * Renders the page buttons.
     * @return array the rendering result
     */
    protected function renderPageButtons()
    {
        if ($this->pageCount < 2) {
            return [];
        }
        $buttons = [];

        // prev page
        if ($this->prevPageLabel !== false) {
            if ($this->page < 1) {
                $this->page = 1;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $this->page - 1, $this->prevPageCssClass, $this->page <= 1, false);
        }

        // internal pages
        list($beginPage, $endPage) = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton($i + 1, $i + 1, $this->internalPageCssClass, false, $i == $this->page);
        }

        if (($page = $this->page + 5) < $this->pageCount)
            $buttons[] = $this->renderPageButton("...", $endPage + 2, $this->internalPageCssClass, false, false);

        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $this->page + 1) >= $this->pageCount - 1) {
                $page = $this->pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page + 1, $this->nextPageCssClass, $this->page >= $this->pageCount - 1, false);
        }
        return $buttons;
    }

    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     * @param string $label the text label for the button
     * @param integer $page the page number
     * @param string $class the CSS class for the page button.
     * @param boolean $disabled whether this page button is disabled
     * @param boolean $active whether this page button is active
     * @return array the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {

        $options = ['class' => $class === '' ? null : $class];
        if ($active) {
            $options["class"] = $this->activePageCssClass;
        }
        if ($disabled) {
            $options["class"] = $this->disabledPageCssClass;
        }

        return ["label" => (string)$label, "class" => $options["class"], "href" => "javascript:void(0);", "page" => (int)$page];
    }

    /**
     * @return array the begin and end pages that need to be displayed.
     */
    protected function getPageRange()
    {
        $beginPage = max(0, $this->page - (int)($this->maxButtonCount / 2));
        if (($endPage = $beginPage + $this->maxButtonCount - 1) >= $this->pageCount) {
            $endPage = $this->pageCount - 1;
            $beginPage = max(0, $endPage - $this->maxButtonCount + 1);
        }
        return [$beginPage, $endPage];
    }
}