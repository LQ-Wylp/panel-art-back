<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ClientRepository $clientRepository;

    public function __construct(EntityManagerInterface $entityManager, ClientRepository $clientRepository)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/clients/register', name: 'client_register', methods: ['POST'])]
    public function register(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        // Vérification des données requises
        $requiredFields = ['email', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new Response("Missing required field: $field", Response::HTTP_BAD_REQUEST);
            }
        }

        // Création d'une nouvelle instance de Client
        $client = new Client();
        $client->setEmail($data['email']);
        $client->setRole($data['roles'] ?? ['ROLE_USER']);
        $client->setFirstname($data['firstname'] ?? '');
        $client->setLastname($data['lastname'] ?? '');
        $client->setAdresse($data['adresse'] ?? '');
        $client->setComplement($data['complement'] ?? '');
        $client->setTown($data['town'] ?? '');
        $client->setPostalCode($data['postalCode'] ?? '');
        $client->setPhone($data['phone'] ?? '');

        // Hashage du mot de passe

        $client->setPassword($data['password']);

        // Validation de l'entité Client
        $errors = $validator->validate($client);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si l'utilisateur existe déjà
        if ($this->clientRepository->findOneBy(['email' => $client->getEmail()])) {
            return new Response('Email already exists', Response::HTTP_CONFLICT);
        }

        // Persister et flusher l'entité Client
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return new Response('Client registered successfully', Response::HTTP_CREATED);
    }

    #[Route('/api/clients/{email}', name: 'client_get', methods: ['GET'])]
    public function getClientByEmail(string $email): Response
    {
        $client = $this->clientRepository->findOneBy(['email' => $email]);

        if (!$client) {
            return new Response('Client not found', Response::HTTP_NOT_FOUND);
        }

        $clientData = [
            'id' => $client->getId(),
            'email' => $client->getEmail(),
            'roles' => $client->getRole(),
            'firstname' => $client->getFirstname(),
            'lastname' => $client->getLastname(),
            'adresse' => $client->getAdresse(),
            'complement' => $client->getComplement(),
            'town' => $client->getTown(),
            'postalCode' => $client->getPostalCode(),
            'phone' => $client->getPhone(),
        ];

        return $this->json($clientData);
    }

    #[Route('/api/clients', name: 'clients_get_all', methods: ['GET'])]
    public function getClients(): Response
    {
        $clients = $this->clientRepository->findAll();

        if (!$clients) {
            return new Response('No clients found', Response::HTTP_NOT_FOUND);
        }

        $clientsData = [];

        foreach ($clients as $client) {
            $clientData = [
                'id' => $client->getId(),
                'email' => $client->getEmail(),
                'roles' => $client->getRole(),
                'firstname' => $client->getFirstname(),
                'lastname' => $client->getLastname(),
                'adresse' => $client->getAdresse(),
                'complement' => $client->getComplement(),
                'town' => $client->getTown(),
                'postalCode' => $client->getPostalCode(),
                'phone' => $client->getPhone(),
            ];

            $clientsData[] = $clientData;
        }

        return $this->json($clientsData);
    }

    #[Route('/api/clients/login/{email}/{password}', name: 'client_login', methods: ['GET'])]
    public function getClientForLogin(string $email, string $password): Response
    {
        $client = $this->clientRepository->findOneBy(['email' => $email, 'password' => $password]);

        if (!$client) {
            return new Response('Bad Log', Response::HTTP_NOT_FOUND);
        }

        $clientData = [
            'id' => $client->getId(),
            'email' => $client->getEmail(),
            'roles' => $client->getRole(),
            'firstname' => $client->getFirstname(),
            'lastname' => $client->getLastname(),
            'adresse' => $client->getAdresse(),
            'complement' => $client->getComplement(),
            'town' => $client->getTown(),
            'postalCode' => $client->getPostalCode(),
            'phone' => $client->getPhone(),
        ];

        return $this->json($clientData);
    }
}
