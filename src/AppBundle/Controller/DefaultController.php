<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Template
     * @Route("/", name="albums")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $albums = $this->get("album_manager")->findAlbums();
        $serialized = $this->get("jms_serializer")->serialize($albums, "json");

        if ($request->isXmlHttpRequest()) {
            return JsonResponse::create(json_decode($serialized));
        } else {
            return [
                'albums' => $serialized
            ];
        }
    }

    /**
     * @Template
     * @Route("/album/{id}", name="album")
     * @Route("/album/{id}/page/{page}", name="album_page")
     * @param Request $request
     * @param $id
     * @param int $page
     * @return array
     */
    public function albumAction(Request $request, $id, $page = 1)
    {
        if ($request->isXmlHttpRequest()) {
            $album = $this->get("album_manager")->findAlbumOr404($id);
            $pagination = $this->get("image_manager")->getAlbumImagesPagination($album, $page);
            $serialized = $this->get("jms_serializer")->serialize($pagination->getItems(), "json");
            $pp = $this->get("knp_paginator.twig.extension.pagination")->render($this->get("twig"), $pagination);

            return JsonResponse::create([
                'data' => json_decode($serialized),
                'pagination' => $pp
            ]);
        } else {
            return $this->forward("AppBundle:Default:index");
        }
    }

    /**
     * @Route("/image/{slug}/{filename}", name="image")
     * @param $slug
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function imageAction($slug, $filename)
    {
        $imageManager = $this->get("image_manager");
        $image = $imageManager->findImageOr404($slug, $filename);

        return $imageManager->imageResponse($image->getFilename(), $image->getOriginalFilename());
    }
}
