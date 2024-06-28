<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;


class ProductControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
{
    parent::tearDown();
    if ($this->entityManager) {
        $this->entityManager->close();
        $this->entityManager = null;
    }
    $this->client = null;
    static::ensureKernelShutdown();
}


    public function testIndex()
    {
        $this->client->request('GET', '/api/products');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testCreate()
{
    $payload = ['name' => 'Test Product', 'description' => 'Test Description'];
    $this->client->request('POST', '/api/products', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
    
    $this->assertResponseStatusCodeSame(201);
    $this->assertResponseHeaderSame('Content-Type', 'application/json');
    
    $response = json_decode($this->client->getResponse()->getContent(), true);
    $this->assertArrayHasKey('id', $response);
    
    // Fetch the created product and assert its data
    $this->client->request('GET', '/api/products/' . $response['id']);
    $this->assertResponseIsSuccessful();
    $fetchedProduct = json_decode($this->client->getResponse()->getContent(), true);
    $this->assertEquals($payload['name'], $fetchedProduct['name']);
    $this->assertEquals($payload['description'], $fetchedProduct['description']);
}

    public function testShow()
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setDescription('Test Description');
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/products/' . $product->getId());
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testUpdate()
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setDescription('Test Description');
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->client->request('PUT', '/api/products/' . $product->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], '{"name":"Updated Product","description":"Updated Description"}');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testDelete()
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setDescription('Test Description');
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->client->request('DELETE', '/api/products/' . $product->getId());
        $this->assertResponseStatusCodeSame(204);
    }

    public function testShowNotFound()
    {
        $this->client->request('GET', '/api/products/9999');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateInvalid()
    {
        $this->client->request('POST', '/api/products', [], [], ['CONTENT_TYPE' => 'application/json'], '{"name":"","description":""}');
        $this->assertResponseStatusCodeSame(400);
    }
}
