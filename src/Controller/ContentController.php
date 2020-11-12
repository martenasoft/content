<?php

namespace MartenaSoft\Content\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('@MartenaSoftContent/content/index.html.twig');
    }
}