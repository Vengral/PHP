<?php
/**
 * TagService tests.
 */

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\TransactionRepository;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TagServiceTest.
 */
class TagServicesTest extends KernelTestCase
{
    /**
     * Tag service.
     *
     * @var TagService|object|null
     */
    private ?TagService $tagService;

    /**
     * Tag repository.
     *
     * @var TagRepository|object|null
     */
    private ?TagRepository $tagRepository;

    /**
     * Transaction repository.
     *
     * @var TransactionRepository|object|null
     */
    private ?TransactionRepository $transactionRepository;

    /**
     * Test save.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testSave(): void
    {
        // given
        $expectedTag = new Tag();
        $expectedTag->setName('Test Tag');
        $expectedTag->setCreatedAt(new \DateTime('now'));
        $expectedTag->setUpdatedAt(new \DateTime('now'));
        // when
        $this->tagService->save($expectedTag);
        $resultTag = $this->tagRepository->findOneById(
            $expectedTag->getId()
        );

        // then
        $this->assertEquals($expectedTag, $resultTag);
    }

    /**
     * Test delete.
     *
     * @covers \App\Service\TagService
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testDelete(): void
    {
        // given
        $expectedTag = new Tag();
        $expectedTag->setName('Test Tag');
        $expectedTag->setCreatedAt(new \DateTime('now'));
        $expectedTag->setUpdatedAt(new \DateTime('now'));
        $this->tagRepository->save($expectedTag);
        $expectedId = $expectedTag->getId();

        // when
        $this->tagService->delete($expectedTag);
        $result = $this->tagRepository->findOneById($expectedId);

        // then
        $this->assertNull($result);
    }

    /**
     * Test find by id.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testFindOneById(): void
    {
        // given
        $expectedTag = new Tag();
        $expectedTag->setName('Test Tag');
        $expectedTag->setCreatedAt(new \DateTime('now'));
        $expectedTag->setUpdatedAt(new \DateTime('now'));
        $this->tagRepository->save($expectedTag);

        // when
        $result = $this->tagService->findOneById($expectedTag->getId());

        // then
        $this->assertEquals($expectedTag->getId(), $result->getId());
    }

    /**
     * Test pagination empty list.
     */
    public function testCreatePaginatedListEmptyList(): void
    {
        // given
        $page = 1;
        $expectedResultSize = 0;
        // when
        $result = $this->tagService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Set up test.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;
        $this->tagRepository = $container->get(TagRepository::class);
        $this->tagService = $container->get(TagService::class);
        $this->transactionRepository = $container->get(TransactionRepository::class);
    }
}
