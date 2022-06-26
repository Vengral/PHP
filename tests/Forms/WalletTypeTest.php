<?php
/**
 * Wallet Type test
 */
namespace App\Tests\Forms;

use App\Entity\Wallet;
use App\Form\Type\WalletType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * class Wallet Type test
 */
class WalletTypeTest extends TypeTestCase
{
    /**
     * Test submiit Validation
     */
    public function testSubmitValidDate()
    {
        $time = new \DateTime('now');
        $formatData = [
            'name' => 'TestWallet',
            'balance' => 2000,
            'createdAt' => $time,
            'updatedAt' => $time,
        ];

        $model = new Wallet();
        $form = $this->factory->create(WalletType::class, $model);

        $expected = new Wallet();
        $expected->setName('TestWallet');
        $expected->setBalance(2000);
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $model);
    }
}
