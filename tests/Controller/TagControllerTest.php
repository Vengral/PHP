<?php
/**
 * Tag Controller Test.
 */

namespace App\Tests\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Tests\WebBaseTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Class Tag COntroller Test
 */
class TagControllerTest extends WebBaseTestCase
{
    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    private TagRepository $repository;
    private string $path = '/tag';
    /**
     * Test setUp.
     */
    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Tag::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }
    /**
     * Test Index.
     */
    public function testIndex(): void
    {
        $this->httpClient->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Tag index');
    }
    /**
     * Test new
     */
    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->httpClient->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->httpClient->submitForm('Save', [
            'tag[name]' => 'Testing',
            'tag[createdAt]' => 'Testing',
            'tag[updatedAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/tag/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    /**
     * Test show.
     */
    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Tag();
        $fixture->setName('My Title');
        $fixture->setCreatedAt(new \DateTime('now'));
        $fixture->setUpdatedAt(new \DateTime('now'));

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Tag');
    }

    /**
     * Test Edit
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Tag();
        $fixture->setName('My Title');
        $fixture->setCreatedAt(new \DateTime('now'));
        $fixture->setUpdatedAt(new \DateTime('now'));

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'tag[name]' => 'Something New',
            'tag[createdAt]' => 'Something New',
            'tag[updatedAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/tag/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
    }

    /**
     * Test Remove
     */
    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Tag();
        $fixture->setName('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/tag/');
    }
}
