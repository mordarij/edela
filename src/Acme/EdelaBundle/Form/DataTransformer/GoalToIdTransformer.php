<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 5/19/14
 * Time: 9:48 PM
 */

namespace Acme\EdelaBundle\Form\DataTransformer;


use Acme\EdelaBundle\Entity\Goal;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class GoalToIdTransformer implements DataTransformerInterface{

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
     * @param Goal $value
     * @return mixed|void
     */
    public function transform($value)
    {
        if ($value === null){
            return "";
        }

        return $value->getId();
    }

    /**
     * @param string $value
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     * @return mixed|void
     */
    public function reverseTransform($value)
    {
        if (!$value){
            return null;
        }

        $goal = $this->om->getRepository('AcmeEdelaBundle:Goal')->find($value);
        if (null === $goal){
            throw new TransformationFailedException(sprintf('Goal %d doesn\'t exists', $value));
        }

        return $goal;
    }
}