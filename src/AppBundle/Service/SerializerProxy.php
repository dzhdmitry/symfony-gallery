<?php

namespace AppBundle\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class SerializerProxy
{
    const DEFAULT_FORMAT = "json";

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $format;

    public function __construct(Serializer $serializer, $format = self::DEFAULT_FORMAT)
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

    /**
     * @param $data
     * @param SerializationContext|null $context
     * @return array
     */
    public function toArray($data, SerializationContext $context = null)
    {
        return $this->serializer->toArray($data, $context);
    }
}
