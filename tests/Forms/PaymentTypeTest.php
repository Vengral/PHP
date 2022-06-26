<?php
/**
 * Payment Type test
 */
namespace App\Tests\Forms;

use App\Entity\Payment;
use App\Form\Type\PaymentType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * class Payment Type test
 */
class PaymentTypeTest extends TypeTestCase
{
    /**
     * test Submit
     */
    public function testSubmitValidData()
    {
        $time = new \DateTime('now');
        $formatData = [
            'name' => 'TestPayment',
            'createdAt' => $time,
            'updatedAt' => $time,
        ];

        $model = new Payment();
        $form = $this->factory->create(PaymentType::class, $model);

        $expected = new Payment();
        $expected->setName('TestPayment');
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}
