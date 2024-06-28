<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\ProductNotFoundException;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

// Route prefix for all methods in this controller
#[Route('/api/products')]
class ProductController extends AbstractController
{
    // Private properties to hold dependencies
    private $entityManager;
    private $productRepository;
    private $validator;

    // Constructor to inject dependencies
    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        ValidatorInterface $validator,
        private SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->validator = $validator;
    }

    #[Route('/debug-token', name: 'debug_token', methods: ['GET'])]
    public function debugToken(Security $security): JsonResponse
    {
        $user = $security->getUser();
        return $this->json([
            'user' => $user ? $user->getUserIdentifier() : 'No user',
            'roles' => $user ? $user->getRoles() : 'No roles',
        ]);
    }

    // Route for getting all products
    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // Fetch all products from the repository
        $products = $this->productRepository->findAll();
        $jsonContent = $this->serializer->serialize($products, 'json', ['groups' => 'product']);
        // Return products as JSON response
        return new JsonResponse($jsonContent, 200, [], true);
    }

    // Route for creating a new product
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Decode JSON request data
        $data = json_decode($request->getContent(), true);

        // Create new Product entity and set data
        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);

        // Validate the product entity
        $errors = $this->validator->validate($product);

        // Check if there are validation errors
        if (count($errors) > 0) {
            // Collect error messages in an array
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            // Return validation errors as JSON response
            return $this->json(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Persist the product entity to the database
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Return the created product as JSON response with 201 status code
        return $this->json($product, Response::HTTP_CREATED);
    }

    // Route for getting a single product by ID
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        // Find the product by ID
        $product = $this->productRepository->find($id);

        // Check if product exists
        if (!$product) {
            // Throw custom exception if product not found
            throw new ProductNotFoundException();
        }

        // Return the product as JSON response
        return $this->json($product);
    }

    // Route for updating a product by ID
    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        // Find the product by ID
        $product = $this->productRepository->find($id);

        // Check if product exists
        if (!$product) {
            // Throw custom exception if product not found
            throw new ProductNotFoundException();
        }

        // Decode JSON request data
        $data = json_decode($request->getContent(), true);

        // Update product entity with new data
        $product->setName($data['name']);
        $product->setDescription($data['description']);

        // Validate the updated product entity
        $errors = $this->validator->validate($product);

        // Check if there are validation errors
        if (count($errors) > 0) {
            // Collect error messages in an array
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            // Return validation errors as JSON response
            return $this->json(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Flush changes to the database
        $this->entityManager->flush();

        // Return the updated product as JSON response
        return $this->json($product);
    }

    // Route for deleting a product by ID
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        // Find the product by ID
        $product = $this->productRepository->find($id);

        // Check if product exists
        if (!$product) {
            // Throw custom exception if product not found
            throw new ProductNotFoundException();
        }

        // Remove the product entity from the database
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Return no content response to indicate successful deletion
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
