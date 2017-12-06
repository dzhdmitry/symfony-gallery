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
     * @Route("/")
     *
     * @param Request $request
     * @return JsonResponse|array
     */
    public function indexAction(Request $request)
    {
        $albums = $this->getDoctrine()->getRepository(Album::class)->findAlbumsWithMaxImages();
        $serializer = $this->get('serializer_proxy');

        if ($request->isXmlHttpRequest()) {
            return JsonResponse::create($serializer->toArray($albums));
        } else {
            return [
                'albums' => $serializer->serialize($albums)
            ];
        }
    }

    /**
     * @Route("/album/{id}")
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
     * @Route("/album/{id}/page/{page}")
     * @param Request $request
     *
     * @param Album $album
     * @param int $page
     * @return JsonResponse|Response
     */
    public function albumPageAction(Request $request, Album $album, $page)
    {
        if ($request->isXmlHttpRequest()) {
            $pagination = $this->get('image_manager')->getAlbumImagesPagination($album, $page);
            $data = $this->get('serializer_proxy')->toArray($pagination->getItems());
            $paginationHtml = $this->get('pagination_renderer')->render($pagination);

            return JsonResponse::create([
                'data' => $data,
                'pagination' => $paginationHtml
            ]);
        } else {
            return $this->forward('AppBundle:Default:index');
        }
    }
}
