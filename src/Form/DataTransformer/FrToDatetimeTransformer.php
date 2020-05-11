<?php 

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrToDatetimeTransformer implements DataTransformerInterface{ 



    public function transform($date)
    {
        if ($date === null) {
          return '';
        }
        return $date->format('d/m/Y');
    }


    public function reverseTransform($dateFr)
    {

        if ($dateFr === null) {
           throw new TransformationFailedException("Fournir une date");
        }

        $date= \DateTime::createFromFormat('d/m/Y',$dateFr);

        if ($date === false) {
            throw new TransformationFailedException("Le format de la date n'est pas correcte");
        }

        return $date;
        
    }

}