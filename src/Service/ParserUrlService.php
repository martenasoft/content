<?php

namespace MartenaSoft\Content\Service;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Content\Exception\MenuConfigNotFound;
use MartenaSoft\Content\Exception\MenuRootNodeNotFound;
use MartenaSoft\Content\Exception\ParseUrlErrorException;
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
        if (empty($rootNode)) {
            throw new MenuRootNodeNotFound();
        }

        if (empty($config = $this->menuRepository->getConfig($rootNode))) {
            throw new MenuConfigNotFound();
        }

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
        $rootUrl = $rootNode->getTransliteratedUrl();
        array_unshift($urlArray, $rootUrl);
        $urlPath = '/' . implode("/", $urlArray);
        $path_ = '/' . $rootUrl .'/'. $path;


        if ($urlPath != $path_) {
            throw new ParseUrlErrorException("Field to compare $urlPath and $path_");
        }

        $result = $this
            ->menuRepository
            ->getQueryBuilder()
            ->where(MenuRepository::getAlias() . '.path=:path')
            ->setParameter('path', $urlPath)
            ->getQuery()
            ->getOneOrNullResult();

        if (empty($result)) {
            throw ParseUrlErrorException("Cant find url by path $urlPath");
        }

        return null;
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
        // $allItems[] = $menu;
        dump($urlArray, $allItems);
        // array_unshift($allItems, $menu);

        $count = count($urlArray);
        foreach ($urlArray as $i => $url) {
            if (empty($allItems[$i])) {
                throw new ParseUrlErrorException([$url], []);
            }
            $item = $allItems[$i]->getTransliteratedUrl();
            dump("$url == $item");
            if ($url == $item && $i == ($count - 1)) {
                return $allItems[$i];
            }
        }
        //  die;
        throw new ParseUrlErrorException([], []);
    }
}
