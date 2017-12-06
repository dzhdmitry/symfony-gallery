<?php

namespace AppBundle\Service;

use AppBundle\Entity\Image;
use Doctrine\ORM\EntityManager;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Paginator
     */
    protected $paginator;

    public function __construct(EntityManager $em, Paginator $paginator)
    {
        $this->em = $em;
        $this->paginator = $paginator;
    }

    /**
     * @param $album
     * @param int $page
     * @return SlidingPagination
     */
    public function getAlbumImagesPagination($album, $page = 1)
    {
        /** @var $pagination SlidingPagination */
        $query = $this->em->getRepository(Image::class)->getAlbumImagesQuery($album);
        $pagination = $this->paginator->paginate($query, $page);

        $pagination->setUsedRoute('app_default_albumpage');

        return $pagination;
    }
}
