<?php
// src/Form/CertificateType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CertificateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('course', TextType::class, [
                'label' => 'Cours',
            ])
            ->add('date', TextType::class, [
                'label' => 'Date',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Générer le certificat',
            ]);
    }
}
