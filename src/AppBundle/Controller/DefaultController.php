<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Album;
use AppBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
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
        //$albums = $this->getDoctrine()->getRepository(Album::class)->findAll();

        $albums = $this->getDoctrine()->getRepository(Album::class)->findAlbumsWithMaxImages();

        return [
            'albums' => $albums
        ];
    }

    /**
     * @Template
     * @Route("/album/{id}", name="album")
     * @Route("/album/{id}/page/{page}", name="album_page")
     * @param Request $request
     * @param $id
     * @param null $page
     * @return array
     */
    public function albumAction(Request $request, $id, $page = null)
    {
        $album = $this->getDoctrine()->getRepository(Album::class)->findOneWithAlbums($id);

        if (!$album) {
            throw $this->createNotFoundException();
        }

        return [
            'album' => $album
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
