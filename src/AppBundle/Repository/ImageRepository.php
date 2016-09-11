<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Album;
use AppBundle\Entity\Image;
use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    /**
     * @param $slug
     * @param $filename
     * @return Image|null
     */
    public function fundBySlugAndOriginalFilename($slug, $filename)
    {
        return $this->findOneBy([
            'slug' => $slug,
            'originalFilename' => $filename
        ]);
    }

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
