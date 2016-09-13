<?php

namespace AppBundle\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class SerializerProxy
{
    const FORMAT = "json";

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $format;

    public function __construct(SerializerInterface $serializer, $format = self::FORMAT)
    {
        $this->serializer = $serializer;
        $this->format = $format;
    }

    /**
     * @param $data
     * @param SerializationContext $context
     * @return string
     */
    public function serialize($data, SerializationContext $context = null)
    {
        return $this->serializer->serialize($data, $this->format, $context);
    }
}
