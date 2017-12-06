<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalFilename;

    /**
     * @Exclude
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Album", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $album;

    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $slug
     * @return Image
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $originalFilename
     * @return Image
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    /**
     * @return string 
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * @param string $filename
     * @return Image
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param Album $album
     * @return Image
     */
    public function setAlbum(Album $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * @return Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return __DIR__ . '/../../../web/images/' . $this->getFilename();
    }

    /**
     * @VirtualProperty
     *
     * @return string
     */
    public function getWebPath()
    {
        return '/images/' . $this->getFilename();
    }

    /**
     * @ORM\PostRemove
     */
    public function removeFile()
    {
        $path = $this->getAbsolutePath();

        if (!is_file($path)) {
            return;
        }

        if (!is_writable($path)) {
            return;
        }

        @unlink($path);
    }
}
