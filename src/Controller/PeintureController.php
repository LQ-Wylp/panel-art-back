<?php

namespace App\Controller;

use App\Entity\Peinture;
use App\Repository\PeintureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PeintureController extends AbstractController
{
    private PeintureRepository $peintureRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(PeintureRepository $peintureRepository, EntityManagerInterface $entityManager)
    {
        $this->peintureRepository = $peintureRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/peintures', name: 'peintures_get_all', methods: ['GET'])]
    public function getPeintures(): Response
    {
        $peintures = $this->peintureRepository->findAll();

        // Vérifier si des peintures ont été trouvées
        if (empty($peintures)) {
            return new Response('No peintures found', Response::HTTP_NOT_FOUND);
        }

        // Transformer les peintures en tableau associatif
        $peinturesData = [];
        foreach ($peintures as $peinture) {
            $peinturesData[] = [
                'id' => $peinture->getId(),
                'title' => $peinture->getTitle(),
                'height' => $peinture->getHeight(),
                'width' => $peinture->getWidth(),
                'description' => $peinture->getDescription(),
                'quantity' => $peinture->getQuantity(),
                'createdAt' => $peinture->getCreatedAt()->format('Y-m-d H:i:s'),
                'method' => $peinture->getMethod(),
                'prize' => $peinture->getPrize(),
            ];
        }

        // Retourner les données des peintures en JSON
        return $this->json($peinturesData);
    }

    #[Route('/api/peintures/{id}', name: 'peinture_get', methods: ['GET'])]
    public function getPeinture(int $id): Response
    {
        $peinture = $this->peintureRepository->find($id);

        // Vérifier si la peinture existe
        if (!$peinture) {
            return new Response('Peinture not found', Response::HTTP_NOT_FOUND);
        }

        // Transformer la peinture en tableau associatif
        $peintureData = [
            'id' => $peinture->getId(),
            'title' => $peinture->getTitle(),
            'height' => $peinture->getHeight(),
            'width' => $peinture->getWidth(),
            'description' => $peinture->getDescription(),
            'quantity' => $peinture->getQuantity(),
            'createdAt' => $peinture->getCreatedAt()->format('Y-m-d H:i:s'),
            'method' => $peinture->getMethod(),
            'prize' => $peinture->getPrize(),
        ];

        // Retourner les données de la peinture en JSON
        return $this->json($peintureData);
    }

    #[Route('/api/peintures', name: 'peinture_add', methods: ['POST'])]
    public function peintureAdd(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        // Création d'une nouvelle instance de Peinture
        $peinture = new Peinture();
        $peinture->setTitle($data['title']);
        $peinture->setHeight($data['height']);
        $peinture->setWidth($data['width']);
        $peinture->setDescription($data['description']);
        $peinture->setQuantity($data['quantity']);
        $peinture->setCreatedAt(new \DateTime($data['createdAt']));
        $peinture->setMethod($data['method']);
        $peinture->setPrize($data['prize']);

        // Validation de l'entité Peinture
        $errors = $validator->validate($peinture);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        // Persister et flusher l'entité Peinture
        $this->entityManager->persist($peinture);
        $this->entityManager->flush();

        return new Response('Peinture registered successfully', Response::HTTP_CREATED);
    }

    #[Route('/api/peintures/{id}', name: 'peinture_update', methods: ['PUT'])]
    public function updatePeinture(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $peinture = $this->peintureRepository->find($id);

        if (!$peinture) {
            return new Response('Peinture not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $peinture->setTitle($data['title'] ?? $peinture->getTitle());
        $peinture->setHeight($data['height'] ?? $peinture->getHeight());
        $peinture->setWidth($data['width'] ?? $peinture->getWidth());
        $peinture->setDescription($data['description'] ?? $peinture->getDescription());
        $peinture->setQuantity($data['quantity'] ?? $peinture->getQuantity());
        $peinture->setCreatedAt(new \DateTime($data['createdAt'] ?? $peinture->getCreatedAt()->format('Y-m-d H:i:s')));
        $peinture->setMethod($data['method'] ?? $peinture->getMethod());
        $peinture->setPrize($data['prize'] ?? $peinture->getPrize());

        $errors = $validator->validate($peinture);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new Response('Peinture updated successfully', Response::HTTP_OK);
    }

    #[Route('/api/peintures/{id}', name: 'peinture_delete', methods: ['DELETE'])]
    public function deletePeinture(int $id): Response
    {
        $peinture = $this->peintureRepository->find($id);

        if (!$peinture) {
            return new Response('Peinture not found', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($peinture);
        $this->entityManager->flush();

        return new Response('Peinture deleted successfully', Response::HTTP_OK);
    }
}
