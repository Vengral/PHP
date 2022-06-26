<?php
/**
 * Transaction type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Operation;
use App\Entity\Payment;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransactionType.
 */
class TransactionType extends AbstractType
{
    /**
     * Tags data transformer.
     */
    private TagsDataTransformer $tagsDataTransformer;

    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Tags data transformer
     */
    public function __construct(TagsDataTransformer $tagsDataTransformer)
    {
        $this->tagsDataTransformer = $tagsDataTransformer;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param array<string, mixed> $options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );
        $builder->add(
            'date',
            DateType::class,
            [
                'label' => 'label.date',
                'required' => true,
            ]
        );
        $builder->add(
            'wallet',
            EntityType::class,
            [
                'class' => Wallet::class,
                'choice_label' => function ($wallet): string {
                    return $wallet->getName();
                },
                'label' => 'label.wallet',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );
        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => function ($category): string {
                    return $category->getName();
                },
                'label' => 'label.category',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );
        $builder->add(
            'operation',
            EntityType::class,
            [
                'class' => Operation::class,
                'choice_label' => function ($operation): string {
                    return $operation->getName();
                },
                'label' => 'label.operation',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );
        $builder->add(
            'payment',
            EntityType::class,
            [
                'class' => Payment::class,
                'choice_label' => function ($payment): string {
                    return $payment->getName();
                },
                'label' => 'label.payment',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );

        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'label.tags',
                'required' => false,
                'attr' => ['max_length' => 128],
            ]
        );

        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );
    }

    /**
     * Configures the options for this type.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Transaction::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     */
    public function getBlockPrefix(): string
    {
        return 'transaction';
    }
}
