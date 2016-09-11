<?php

namespace AppBundle\Repository;

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
}
