<?php

namespace App\Form;

use App\Entity\Ad;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends ApplicationType
{
   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('title',TextType::class,$this->getConfiguration('Titre','Insérez un titre'))
             ->add('slug',TextType::class,$this->getConfiguration('Alias','Personalisez un alias pour générer un URL',['required'=>false]))
            ->add('coverImage', UrlType::class,$this->getConfiguration('Image decouverture','Insérer une image'))
            ->add('introduction',TextType::class,$this->getConfiguration('Résumé','Présentez votre bien'))
            ->add('content',TextareaType::class,$this->getConfiguration('Description détaillé','Décrivez vos services'))
            ->add('rooms',IntegerType::class,$this->getConfiguration('Nombre de chambres','Nombre de chambres'))
            ->add('price',MoneyType::class,$this->getConfiguration('Prix','Prix des chambres par nuit'))
            ->add('images',CollectionType::class,['entry_type'=>ImageType::class,'allow_add'=>true,'allow_delete'=>true])
            //->add('save', SubmitType::class,['label'=>'Créer!','attr'=>['class'=>'btn btn-warning btn-large']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
