<?php

namespace App\Form;
use App\Entity\Marque;
use App\Entity\ModeleChaussure;
use App\Entity\Photo;
use App\Entity\Taille;
use Symfony\Component\Validator\Constraints\Url;
//use http\Url;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeleChaussureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,array('label'=>'nom',
            'attr'=>array('placeholder'=>'entrez le nom de la chaussure')))
      
            ->add('prix',MoneyType::class,array('label'=>'prix',
            'attr'=>array('placeholder'=>'entrez le prix de la chaussure')))
            ->add('description', TextareaType::class,array('label'=>'description',
            'attr'=>array('placeholder'=>'faites une brêve description de la chaussure ')))
         ->add('coverImage',FileType::class,array(
             'label'=>'Importez une image de couverture',
             'data_class'=>null,
'attr'=>array('placeholder'=>'importez une image de couverture',
             
          
                 'data_class'=>null
                 

             )))


         

            ->setMethod("POST")


            ->add('marque',EntityType::class,[
                'class'=>Marque::class,
                'choice_label'=>'nom'
            ])


          ->add(
               'photos',

               FileType::class,array(
                  'label'=>'importez autres images',
                  'data_class'=>null,
                  'mapped' => false,
                    
       
       'attr'=>array('placeholder'=>'importez une ou plusieurs   images de couverture',

              'by_reference' => true,
                  'multiple' => true,
                   'data_class'=>null,
                   'mapped'=>false,
                 'required'=>false,))

       )
            ;
     
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModeleChaussure::class,
        ]);
    }
}