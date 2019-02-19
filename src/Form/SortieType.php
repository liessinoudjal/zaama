<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\FOSUserBundle\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\TypeSortie;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateSortie', DateTimeType::class, array(
            'label' => 'Date', 
            "required"=>true,
            'years' => range(date("Y"), date('Y') - 120),
            'attr' => ["class"=>"date-sortie","autocomplete"=>"off"],
            'widget' => 'single_text',
            'input' => 'datetime',
            'format' => 'dd-MM-yyyy'
            ))
            ->add('portable')
            //->add('photo')
            ->add('intitule')
            ->add('entreFille')
            ->add('presentation', TextareaType::class)
            ->add('nbPersonneMax', IntegerType::class, [
                'attr' => [
                    'min' => 2,
                    'max' => 20
                ]
            ])
            ->add('typeSortie', EntityType::class, [
                'class' => TypeSortie::class,
                'choice_label' => 'nom',
            ])
            ->add('heure', ChoiceType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 23
                ],
                'choices' => range(0,23)
            ])
            ->add('minute', ChoiceType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 59
                ],
                'choices' => $this->KeyToValue(range(0,55,5))
            ])
        ;
    }
    private function KeyToValue(array $array){
        $tab=[];
        foreach($array as $key =>$value){
            $tab[$value]=$value;
        } 
        return $tab;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
