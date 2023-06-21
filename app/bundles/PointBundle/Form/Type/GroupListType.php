<?php

namespace Mautic\PointBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Mautic\PointBundle\Entity\Group;
use Mautic\PointBundle\Entity\GroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<GroupListType>
 */
class GroupListType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var GroupRepository
     */
    private $repo;

    public function __construct(EntityManager $em, GroupRepository $repo)
    {
        $this->em         = $em;
        $this->repo       = $repo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (true === $options['return_entity']) {
            $transformer = new IdToEntityModelTransformer($this->em, Group::class, 'id');
            $builder->addModelTransformer($transformer);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $groups  = $this->repo->getEntities();
                $choices = [];
                foreach ($groups as $l) {
                    $choices[$l->getName()] = $l->getId();
                }

                return $choices;
            },
            'label'             => 'mautic.point.group.form.group',
            'label_attr'        => ['class' => 'control-label'],
            'multiple'          => false,
            'required'          => false,
            'return_entity'     => true,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'group';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
