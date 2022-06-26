<?php

namespace App\Tests\Forms;

use App\Entity\Operation;
use App\Form\Type\OperationType;
use Symfony\Component\Form\Test\TypeTestCase;

class OperationTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $time = new \DateTime('now');
        $formatData = [
            'name' => 'TestOperation',
            'createdAt' => $time,
            'updatedAt' => $time
        ];

        $model = new Operation();
        $form = $this->factory->create(OperationType::class, $model);

        $expected = new Operation();
        $expected->setName('TestOperation');
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}