<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 5/26/14
 * Time: 5:12 PM
 */

namespace Acme\EdelaBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

class PeriodicityTransformer implements DataTransformerInterface
{

    private $choices;
    function __construct($choices)
    {
        $this->choices = $choices;
    }

    public function transform($value)
    {
        $result = array();
        $choices = $this->choices;
        foreach($choices as $key => $choice){
            if ($value & (1 << $key)){
                $result[$key] = $key;
            }
        }
        return $result;
    }

    public function reverseTransform($value)
    {
        $result = 0;
        foreach($value as $ind){
            $result |= (1 << $ind);
        }

        return $result;
    }
}