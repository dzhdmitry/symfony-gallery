<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Album;
use AppBundle\Entity\Image;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

class LoadAlbums extends AbstractFixture implements FixtureInterface
{
    /**
     * @var string
     */
    protected $sourcePath;

    /**
     * @var string
     */
    protected $destinationPath;

    /**
     * @var string[]
     */
    protected $files;

    public function __construct()
    {
        $this->sourcePath = realpath("app/data");
        $this->destinationPath = realpath("web/images");
        $this->files = array_values(array_diff(scandir($this->sourcePath), ['.', '..']));
    }

    public function load(ObjectManager $manager)
    {
        $albumsNames = [
            "Nature",
            "Photos",
            "Wallpapers",
            "People",
            "Other",
        ];

        foreach ($albumsNames as $i => $name) {
            if ($i == 0) {
                $min = 5;
                $max = 5;
            } else {
                $min = 20;
                $max = 40;
            }

            $imagesAmount = rand($min, $max);
            $album = new Album();

            $album->setName($name);

            for ($j = 0; $j < $imagesAmount; $j++) {
                $image = new Image();
                $sourceFilename = $this->getRandomSourceFilename();
                $slug = $this->generateUniqueRandomString();
                $file = new File($this->sourcePath . "/" . $sourceFilename);
                $destinationFilename = sprintf("%s.%s", $slug, $file->guessExtension());

                copy($this->sourcePath . "/" . $sourceFilename, $this->destinationPath . "/" . $destinationFilename);

                $image->setSlug($slug);
                $image->setOriginalFilename($sourceFilename);
                $image->setFilename($destinationFilename);
                $album->addImage($image);
            }

            $manager->persist($album);
        }

        $manager->flush();
    }

    /**
     * @return string
     */
    public function getRandomSourceFilename()
    {
        $index = rand(0, count($this->files) - 1);

        return $this->files[$index];
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateUniqueRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $string = "";

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $string;
    }
}
