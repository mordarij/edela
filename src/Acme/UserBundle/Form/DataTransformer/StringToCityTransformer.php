<?php

namespace Acme\UserBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class StringToCityTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param string $locale
     * @return Language|null
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($locale)
    {
        // Here, you can use Object manager, get your repository et get the "Language" Entity with your local string
        // return the entity or thow a TransformationFailedException if none are found
    }

    /**
     * @param Language $language
     * @return string
     */
    public function reverseTransform($city)
    {
        if (null === $city) {
            return "";
        }

        return $city->getId();
    }
}
