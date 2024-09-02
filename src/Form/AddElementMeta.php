<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddElementMeta extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', TextType::class, [
                'label' => 'Category',
                'required' => true,
            ])
            ->add('section', TextType::class, [
                'label' => 'Section',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add Material Type',
            ]);
    }
}
