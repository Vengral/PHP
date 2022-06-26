<?php
/**
 * Category Type list
 */
namespace App\Tests\Forms;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * class Category Type Test.
 */
class CategoryTypeTest extends TypeTestCase
{
    /**
     * Test Submit Valid
     */
    public function testSubmitValidDate()
    {
        $time = new \DateTime('now');
        $formatData = [
            'name' => 'TestCategory',
            'createdAt' => $time,
            'updatedAt' => $time,
        ];

        $model = new Category();
        $form = $this->factory->create(CategoryType::class, $model);

        $expected = new Category();
        $expected->setName('TestCategory');
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);
        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}
