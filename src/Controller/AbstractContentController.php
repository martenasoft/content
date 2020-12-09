<?php

namespace MartenaSoft\Content\Controller;

use Doctrine\ORM\QueryBuilder;
use MartenaSoft\Common\Controller\AbstractCommonController;
use MartenaSoft\Common\Entity\PageData;
use MartenaSoft\Common\Entity\PageDataInterface;
use MartenaSoft\Common\EventSubscriber\CommonSubscriber;
use MartenaSoft\Content\Entity\ConfigInterface;
use MartenaSoft\Content\Exception\ParseUrlErrorException;
use MartenaSoft\Content\Service\ParserUrlService;
use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Repository\MenuRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        try {

            $activeData = $this
                ->parserUrlService
                ->getActiveEntityByUrl($rootNode, $path, $this->getFindUrlQueryBuilder());

            if (($page = $request->query->getInt('page', 1)) <= 1) {
                $page = $this->parserUrlService->getPage();
            }


            $pageData
                ->setActiveData($activeData)
                ->setRootNode($rootNode)
                ->setPage($page)
                ->setIsDetail($this->parserUrlService->isDetailPage());

            $pageData->setContentConfig($this->getConfig($pageData->getPath()));
            $pageData->setPath($path);

            return $this->getResponse($pageData);
        } catch (ParseUrlErrorException $exception) {
            throw new NotFoundHttpException();
        } catch (\Throwable $exception) {
            throw $exception;
        }

    }

    protected function getFindUrlQueryBuilder(): ?QueryBuilder
    {
        return null;
    }

    abstract protected function getRootMenuEntity(): ?MenuInterface;

    abstract protected function getConfig(string $url): ?ConfigInterface;

    abstract protected function getResponse(PageDataInterface $pageData): Response;
}
