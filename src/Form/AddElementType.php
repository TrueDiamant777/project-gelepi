<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Service\CsvDataExplorer\CsvManager;
use App\Service\CsvDataExplorer\CsvFilter;

class AddElementType extends AbstractType
{
    private $csvManager;

    public function __construct(CsvManager $csvManager)
    {
        $this->csvManager = $csvManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typeChoices = $this->csvManager->getMetaChoices();
        $etatGarantiChoices = $this->csvManager->getEtatGarantiChoices();
        $etatSanteChoices = $this->csvManager->getEtatSanteChoices();

        $builder
            ->add('Arriver', DateType::class, [
                'label' => 'Date d\'arriver',
                'required' => true,
            ])
            ->add('DepartGaranti', DateType::class, [
                'label' => 'Depart de la garanti',
                'required' => true,
            ])
            ->add('DureeGaranti', TextType::class, [
                'label' => 'Durée Max. de la garanti (année)',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouté',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}