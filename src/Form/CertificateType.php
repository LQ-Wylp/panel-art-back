<?php
// src/Form/CertificateType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CertificateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Titre de la peinture',
        ])
        ->add('width', TextType::class, [
            'label' => 'Largeur de la peinture',
        ])
        ->add('height', TextType::class, [
            'label' => 'Hauteur de la peinture',
        ])
        ->add('date', DateType::class, [
            'label' => 'Date de création',
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
        ])
        ->add('medium', TextType::class, [
            'label' => 'Médium de la peinture',
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Générer le certificat',
        ]);
    }
}
