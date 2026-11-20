<?php
declare(strict_types=1);
namespace App\Form;
use App\Entity\Team;
use App\Entity\WorkLocation;
use App\Repository\WorkLocationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
final class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class)->add('location', EntityType::class, ['class' => WorkLocation::class, 'choice_label' => 'name', 'required' => false, 'placeholder' => 'No location', 'query_builder' => static fn (WorkLocationRepository $r) => $r->createQueryBuilder('l')->andWhere('l.isTrashed = false')->orderBy('l.name')]);
    }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => Team::class]); }
}
