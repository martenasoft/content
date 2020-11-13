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
    protected const DETAIL_SLIDER = '.html';
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
        $lastUrl = end($urlArray);
        if (is_numeric($lastUrl) && ($page = intval($lastUrl)) > 0) {
            $this->page = $page;
            $lastUrl = isset($urlArray[count($urlArray) - 2]) ? $urlArray[count($urlArray) - 2] : null;
        }

        if (preg_match('/(.*)\\'.self::DETAIL_SLIDER.'/', $lastUrl, $matches) && isset($matches[1])) {
            $lastUrl = $matches[1];
        }

        if (empty($rootNode) || empty($config = $rootNode->getConfig())) {
            return null;
        }

        switch ($config->getUrlPathType()) {
            case Config::URL_TYPE_PATH:
                    $this->validateUrlPathType($rootNode, $urlArray);
                break;
        }

        die;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    protected function validateUrlPathType(MenuInterface $menu, array $urlArray): void
    {
        $allItems = $this->menuRepository->getAllSubItemsQueryBuilder($menu)->getQuery()->getResult();

        $firstMenu = array_shift($urlArray);

        if ($menu->getTransliteratedUrl() != $firstMenu) {
            throw new ParseUrlErrorException([$menu], [$firstMenu]);
        }

        foreach ($allItems as $i=>$item) {
            if (empty($itemUrl = $urlArray[$i])) {
                throw new ParseUrlErrorException([$item], [$itemUrl]);
            }

            $itemUrl = str_replace(self::DETAIL_SLIDER, '', $itemUrl);

            if ($itemUrl != $item->getTransliteratedUrl()) {
                throw new ParseUrlErrorException([$item], [$itemUrl]);
            }
            dump($item, $itemUrl);
        }
        die;
       // dump($menu, $urlArray, $allItems);
    }
}
