<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Album;
use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    /**
     * @param Album $album
     * @return \Doctrine\ORM\Query
     */
    public function getAlbumImagesQuery(Album $album)
    {
        return $this->createQueryBuilder("image")
            ->where("image.album = :album")
            ->setParameter("album", $album)
            ->getQuery();
    }
}
