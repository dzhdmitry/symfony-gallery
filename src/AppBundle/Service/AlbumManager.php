<?php

namespace AppBundle\Service;

use AppBundle\Entity\Album;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AlbumManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $id
     * @return Album
     */
    public function findAlbumOr404($id)
    {
        $album = $this->em->getRepository(Album::class)->find($id);

        if (!$album) {
            throw new NotFoundHttpException("Album not found");
        }

        return $album;
    }

    /**
     * @return \AppBundle\Entity\Album[]
     */
    public function findAlbums()
    {
        return $this->em->getRepository(Album::class)->findAlbumsWithMaxImages();
    }
}
