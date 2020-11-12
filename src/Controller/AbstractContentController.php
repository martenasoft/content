<?php

namespace MartenaSoft\Content\Controller;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractContentController extends AbstractController
{
    protected const DETAIL_SLIDER = '.html';
    private int $page = 1;
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    protected function getActiveEntityByUrl(Menu $rootNode, string $path): ?CommonEntityInterface
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

        //getAllSubItemsQueryBuilder

        $this->menuRepository->findOneByUrl();
        dump($this->menuRepository);


        die;
    }

    protected function getMenuRepository(): MenuRepository
    {
        return $this->menuRepository;
    }

    protected function getPage(): int
    {
        return $this->page;
    }
}
