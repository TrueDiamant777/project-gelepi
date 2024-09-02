<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AddElementType extends AbstractType
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Paths to the CSV files
        $metaListPath = $this->params->get('kernel.project_dir') . '/public/GLPISYS/MaterielType.csv';
        $etatGarantiPath = $this->params->get('kernel.project_dir') . '/public/GLPISYS/MetaDataGaranti.csv';
        $etatSantePath = $this->params->get('kernel.project_dir') . '/public/GLPISYS/MetaDataSante.csv';

        // Load material types from CSV
        $materialTypes = $this->loadCsvChoices($metaListPath, 3, 0);

        // Load warranty states from CSV
        $warrantyStates = $this->loadCsvChoices($etatGarantiPath, 1, 0);

        // Load health states from CSV
        $healthStates = $this->loadCsvChoices($etatSantePath, 1, 0);

        // Build the form with the loaded choices
        $builder
            ->add('material_type', ChoiceType::class, [
                'choices' => $materialTypes,
                'label' => 'Material Type',
                'placeholder' => 'Select Material Type',
            ])
            ->add('warranty_state', ChoiceType::class, [
                'choices' => $warrantyStates,
                'label' => 'Warranty State',
                'placeholder' => 'Select Warranty State',
            ])
            ->add('health_state', ChoiceType::class, [
                'choices' => $healthStates,
                'label' => 'Health State',
                'placeholder' => 'Select Health State',
            ])
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

    private function loadCsvChoices(string $filePath, int $labelIndex, int $valueIndex): array
    {
        $choices = [];

        if (file_exists($filePath)) {
            if (($handle = fopen($filePath, 'r')) !== false) {
                fgetcsv($handle, 1000, ';'); // Skip header
                while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                    if (isset($data[$labelIndex]) && isset($data[$valueIndex])) {
                        $choices[$data[$labelIndex]] = $data[$valueIndex];
                    }
                }
                fclose($handle);
            }
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // No need for these since we load data directly in the form class
        ]);
    }
}
