<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Template
     * @Route("/", name="albums")
     * @return array
     */
    public function indexAction()
    {
        $albums = $this->get("album_manager")->findAlbums();

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
        $album = $this->get("album_manager")->findAlbumOr404($id);
        $pagination = $this->get("image_manager")->getAlbumImagesPagination($album, $page);

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
        $imageManager = $this->get("image_manager");
        $image = $imageManager->findImageOr404($slug, $filename);

        return $imageManager->imageResponse($image->getFilename(), $image->getOriginalFilename());
    }
}
