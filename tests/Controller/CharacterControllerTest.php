<?php

namespace App\Tests\Controller;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CharacterControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $characterRepository;
    private string $path = '/character/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->characterRepository = $this->manager->getRepository(Character::class);

        foreach ($this->characterRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Character index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'character[identifier]' => 'Testing',
            'character[name]' => 'Testing',
            'character[slug]' => 'Testing',
            'character[kind]' => 'Testing',
            'character[surname]' => 'Testing',
            'character[caste]' => 'Testing',
            'character[knowledge]' => 'Testing',
            'character[intelligence]' => 'Testing',
            'character[strength]' => 'Testing',
            'character[image]' => 'Testing',
            'character[creation]' => 'Testing',
            'character[modification]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->characterRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setIdentifier('My Title');
        $fixture->setName('My Title');
        $fixture->setSlug('My Title');
        $fixture->setKind('My Title');
        $fixture->setSurname('My Title');
        $fixture->setCaste('My Title');
        $fixture->setKnowledge('My Title');
        $fixture->setIntelligence('My Title');
        $fixture->setStrength('My Title');
        $fixture->setImage('My Title');
        $fixture->setCreation('My Title');
        $fixture->setModification('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Character');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setIdentifier('Value');
        $fixture->setName('Value');
        $fixture->setSlug('Value');
        $fixture->setKind('Value');
        $fixture->setSurname('Value');
        $fixture->setCaste('Value');
        $fixture->setKnowledge('Value');
        $fixture->setIntelligence('Value');
        $fixture->setStrength('Value');
        $fixture->setImage('Value');
        $fixture->setCreation('Value');
        $fixture->setModification('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'character[identifier]' => 'Something New',
            'character[name]' => 'Something New',
            'character[slug]' => 'Something New',
            'character[kind]' => 'Something New',
            'character[surname]' => 'Something New',
            'character[caste]' => 'Something New',
            'character[knowledge]' => 'Something New',
            'character[intelligence]' => 'Something New',
            'character[strength]' => 'Something New',
            'character[image]' => 'Something New',
            'character[creation]' => 'Something New',
            'character[modification]' => 'Something New',
        ]);

        self::assertResponseRedirects('/character/');

        $fixture = $this->characterRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getIdentifier());
        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getKind());
        self::assertSame('Something New', $fixture[0]->getSurname());
        self::assertSame('Something New', $fixture[0]->getCaste());
        self::assertSame('Something New', $fixture[0]->getKnowledge());
        self::assertSame('Something New', $fixture[0]->getIntelligence());
        self::assertSame('Something New', $fixture[0]->getStrength());
        self::assertSame('Something New', $fixture[0]->getImage());
        self::assertSame('Something New', $fixture[0]->getCreation());
        self::assertSame('Something New', $fixture[0]->getModification());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setIdentifier('Value');
        $fixture->setName('Value');
        $fixture->setSlug('Value');
        $fixture->setKind('Value');
        $fixture->setSurname('Value');
        $fixture->setCaste('Value');
        $fixture->setKnowledge('Value');
        $fixture->setIntelligence('Value');
        $fixture->setStrength('Value');
        $fixture->setImage('Value');
        $fixture->setCreation('Value');
        $fixture->setModification('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/character/');
        self::assertSame(0, $this->characterRepository->count([]));
    }
}
