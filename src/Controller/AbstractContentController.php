<?php

namespace MartenaSoft\Content\Controller;

use MartenaSoft\Common\Controller\AbstractCommonController;
use MartenaSoft\Common\Entity\PageData;
use MartenaSoft\Common\Entity\PageDataInterface;
use MartenaSoft\Common\EventSubscriber\CommonSubscriber;
use MartenaSoft\Content\Entity\ConfigInterface;
use MartenaSoft\Content\Service\ParserUrlService;
use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Repository\MenuRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractContentController extends AbstractCommonController
{
    protected ParserUrlService $parserUrlService;
    protected MenuRepository $menuRepository;

    public function __construct(ParserUrlService $parserUrlService, MenuRepository $menuRepository)
    {
        $this->parserUrlService = $parserUrlService;
        $this->menuRepository = $menuRepository;
    }

    public function page(Request $request, string $path): Response
    {
        $rootNode = $this->getRootMenuEntity();
        $pageData = new PageData();

        if (empty($rootNode)) {
            throw new \Exception("root node not found");
        }

        $activeMenu = $this->parserUrlService->getActiveEntityByUrl($rootNode, $path);
        $pageData
            ->setActiveMenu($activeMenu)
            ->setRootNode($rootNode)
            ->setPage($this->parserUrlService->getPage())
            ->setIsDetail($this->parserUrlService->isDetailPage());

        $pageData->setContentConfig($this->getConfig($pageData->getPath()));
        $pageData->setPath($path);

        return $this->getResponse($pageData);
    }



    abstract protected function getRootMenuEntity(): ?MenuInterface;

    abstract protected function getConfig(string $url): ?ConfigInterface;

    abstract protected function getResponse(PageDataInterface $pageData): Response;
}
