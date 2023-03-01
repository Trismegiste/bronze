<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of HumanType
 *
 * @author flo
 */
class HumanType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', TextType::class)
                ->add('save', SubmitType::class)
        ;
    }

}
