<?php
declare(strict_types=1);
namespace App\Form;
use App\Entity\WorkLocation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
final class WorkLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void { $builder->add('name', TextType::class)->add('address', TextareaType::class, ['required' => false]); }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => WorkLocation::class]); }
}
