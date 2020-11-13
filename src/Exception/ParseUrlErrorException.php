<?php

namespace MartenaSoft\Content\Exception;

use MartenaSoft\Common\Exception\CommonException;
use Throwable;

class ParseUrlErrorException extends CommonException
{
    private array $menuItems = [];
    private array $urlItems = [];

    public function __construct(array $menuItems, array $urlItems, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->setMenuItems($menuItems);
        $this->setUrlItems($urlItems);
        parent::__construct($message, $code, $previous);
    }

    public function getMenuItems(): array
    {
        return $this->menuItems;
    }

    protected function setMenuItems(array $menuItems): self
    {
        $this->menuItems = $menuItems;
        return $this;
    }

    public function getUrlItems(): array
    {
        return $this->urlItems;
    }

    protected function setUrlItems(array $urlItems): self
    {
        $this->urlItems = $urlItems;
        return $this;
    }


}