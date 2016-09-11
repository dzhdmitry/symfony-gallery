<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Album;
use AppBundle\Entity\Image;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends Controller
{
    /**
     * @Template
     * @Route("/", name="albums")
     * @return array
     */
    public function indexAction()
    {
        $albums = $this->getDoctrine()->getRepository(Album::class)->findAlbumsWithMaxImages();

        return [
            'albums' => $albums
        ];
    }

    /**
     * @Template
     * @Route("/album/{id}", name="album")
     * @Route("/album/{id}/page/{page}", name="album_page")
     * @param $id
     * @param int $page
     * @return array
     */
    public function albumAction($id, $page = 1)
    {
        $album = $this->getDoctrine()->getRepository(Album::class)->find($id);

        if (!$album) {
            throw $this->createNotFoundException();
        }

        /** @var $pagination SlidingPagination */
        $paginator = $this->get('knp_paginator');
        $query = $this->getDoctrine()->getRepository(Image::class)->getAlbumImagesQuery($album);
        $pagination = $paginator->paginate($query, $page);

        $pagination->setUsedRoute("album_page");

        return [
            'album' => $album,
            'pagination' => $pagination
        ];
    }

    /**
     * @Route("/image/{slug}/{filename}", name="image")
     * @param $slug
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function imageAction($slug, $filename)
    {
        /** @var $image Image */
        $image = $this->getDoctrine()->getRepository(Image::class)->fundBySlugAndOriginalFilename($slug, $filename);

        if (!$image) {
            throw $this->createNotFoundException();
        }

        return $this->imageResponse($image->getFilename(), $image->getOriginalFilename());
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
