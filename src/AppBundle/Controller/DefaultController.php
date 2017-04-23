<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Album;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Template
     * @Route("/", name="albums")
     *
     * @param Request $request
     * @return JsonResponse|array
     */
    public function indexAction(Request $request)
    {
        $albums = $this->getDoctrine()->getManager()->getRepository(Album::class)->findAlbumsWithMaxImages();
        $serialized = $this->get("serializer_proxy")->serialize($albums);

        if ($request->isXmlHttpRequest()) {
            return JsonResponse::create(json_decode($serialized));
        } else {
            return [
                'albums' => $serialized
            ];
        }
    }

    /**
     * @Route("/album/{id}", name="album")
     * @param Request $request
     *
     * @param Album $album
     * @return JsonResponse|Response
     */
    public function albumAction(Request $request, Album $album)
    {
        return $this->albumPageAction($request, $album, 1);
    }

    /**
     * @Route("/album/{id}/page/{page}", name="album_page")
     * @param Request $request
     *
     * @param Album $album
     * @param int $page
     * @return JsonResponse|Response
     */
    public function albumPageAction(Request $request, Album $album, $page)
    {
        if ($request->isXmlHttpRequest()) {
            $pagination = $this->get("image_manager")->getAlbumImagesPagination($album, $page);
            $data = $this->get("serializer_proxy")->serialize($pagination->getItems());
            $paginationHtml = $this->get("pagination_renderer")->render($pagination);

            return JsonResponse::create([
                'data' => json_decode($data),
                'pagination' => $paginationHtml
            ]);
        } else {
            return $this->forward("AppBundle:Default:index");
        }
    }
}
