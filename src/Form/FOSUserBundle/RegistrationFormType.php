<?php
namespace App\Form\FOSUserBundle;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use App\Entity\FOSUserBundle\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RegistrationFormType extends BaseType
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('email')->remove('username')->remove('plainPassword');
        $builder
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'required'=>true,
                'choices' => array('' => NULL, 'Homme' => 'H','Femme'=>'F')
                ])
            ->add('prenom',TextType::class,['required'=>true,])
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('dateBirth', DateType::class, array('label' => 'Date de naissance', "required"=>true,
            'years' => range(date("Y"), date('Y') - 120),
            'attr' => ["class"=>"date-birth","autocomplete"=>"off"],
            'widget' => 'single_text',
            'input' => 'datetime',
			'format' => 'dd-MM-yyyy'
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_name'=>'first',
                'first_options' => array('label' => 'form.password'),
                'second_name'=>'second',
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
                'required'=>true,
                'help' => 'Au moins 8 caractères alpha numeriques (chiffre + minuscule + majuscule)',
            ))
        ;
        dump($builder);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }
    public function getBlockPrefix()
    {
        return 'zaama_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}   