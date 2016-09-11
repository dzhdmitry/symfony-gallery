<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Album;
use Doctrine\ORM\EntityRepository;

class AlbumRepository extends EntityRepository
{
    /**
     * Find all albums with maximum $maxImages images per album
     *
     * @param int $maxImages
     * @return Album[]
     */
    public function findAlbumsWithMaxImages($maxImages = 10)
    {
        $query = $this->createQueryBuilder("album")
            ->select("album", "images")
            ->leftJoin("album.images", "images")
            ->where('(
                SELECT COUNT(img.id)
                FROM AppBundle\Entity\Image img
                WHERE img.album = images.album AND img.id <= images.id
                ) <= :maxImages')
            ->setParameter("maxImages", $maxImages);

        return $query->getQuery()->execute();
    }
}
