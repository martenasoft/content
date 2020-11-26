<?php

namespace MartenaSoft\Content\Service;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Content\Exception\ParseUrlErrorException;
use MartenaSoft\Menu\Entity\Config;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Repository\MenuRepository;

class ParserUrlService
{
    public const DETAIL_SLIDER = '.html';
    private bool $isDetailPage = false;
    private int $page = 1;
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function getActiveEntityByUrl(MenuInterface $rootNode, string $path): ?CommonEntityInterface
    {
        $url = preg_replace('/\/{2,}/', '/', $path);
        $urlArray = explode('/', $url);
        $lastUrl = array_pop($urlArray);

        if (is_numeric($lastUrl) && ($page = intval($lastUrl)) > 0) {
            $this->page = $page;
            $lastUrl = isset($urlArray[count($urlArray) - 2]) ? $urlArray[count($urlArray) - 2] : null;
        }

        if (preg_match('/(.*)\\' . self::DETAIL_SLIDER . '/', $lastUrl, $matches) && isset($matches[1])) {
            $lastUrl = $matches[1];
            $this->isDetailPage = true;
        }

        $urlArray[] = $lastUrl;

        if (empty($rootNode) || empty($config = $rootNode->getConfig())) {
            return null;
        }

        $lastMenuItem = null;
        switch ($config->getUrlPathType()) {
            case Config::URL_TYPE_PATH:
               $lastMenuItem = $this->validateUrlPathType($rootNode, $urlArray);
                break;
        }
        return $lastMenuItem;
    }

    public function isDetailPage(): bool
    {
        return $this->isDetailPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    protected function validateUrlPathType(MenuInterface $menu, array $urlArray): ?MenuInterface
    {
        $allItems = $this->menuRepository->getAllSubItemsQueryBuilder($menu)->getQuery()->getResult();
        array_unshift($allItems, $menu);
        $count = count($urlArray);
        foreach ($urlArray as $i => $url) {
            if (empty($allItems[$i])) {
                throw new ParseUrlErrorException([$url], []);
            }
            $item = $allItems[$i]->getTransliteratedUrl();
            if ($url != $item) {
                throw new ParseUrlErrorException([$url], [$item]);
            }
            if ($i == $count - 1) {
                return $allItems[$i];
            }
        }
    }
}
