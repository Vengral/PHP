<?php
/**
 * Tag type.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TagType.
 */
class TagType extends AbstractType
{
    /**
     * @param FormBuilderInterface options $builder
     * @param array               options  $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name');
    }
}
