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
     * @param $slug
     * @param $filename
     * @return Image|null
     */
    public function findImageOr404($slug, $filename)
    {
        $image = $this->em->getRepository(Image::class)->fundBySlugAndOriginalFilename($slug, $filename);

        if (!$image) {
            throw new NotFoundHttpException("Image not found");
        }

        return $image;
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

        $pagination->setUsedRoute("album_page");

        return $pagination;
    }

    /**
     * @param $filename
     * @param $originalFilename
     * @param bool $download
     * @return BinaryFileResponse
     */
    public function imageResponse($filename, $originalFilename = null, $download = false)
    {
        $filePath = __DIR__ . "/../../../web/images/" . $filename;
        $response = new BinaryFileResponse($filePath, 200, [], true);

        if ($download) {
            if (!$originalFilename) {
                $originalFilename = $filename;
            }

            $ascii = mb_convert_encoding($originalFilename, "ascii");

            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $originalFilename, $ascii);
        }

        return $response;
    }
}
