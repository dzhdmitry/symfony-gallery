<?php

namespace AppBundle\Service;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Bundle\PaginatorBundle\Twig\Extension\PaginationExtension;
use Twig_Environment;

class PaginationRenderer
{
    /**
     * @var PaginationExtension
     */
    protected $pagination;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    public function __construct(PaginationExtension $pagination, Twig_Environment $twig)
    {
        $this->pagination = $pagination;
        $this->twig = $twig;
    }

    /**
     * @param SlidingPagination $pagination
     * @return string
     */
    public function render(SlidingPagination $pagination)
    {
        return $this->pagination->render($this->twig, $pagination);
    }
}
