<?php

namespace App\Controller;

use App\Entity\Vente;
use App\Repository\ClientRepository;
use App\Repository\PeintureRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VenteController extends AbstractController
{
    private VenteRepository $venteRepository;
    private EntityManagerInterface $entityManager;
    private ClientRepository $clientRepository;
    private PeintureRepository $peintureRepository;

    public function __construct(VenteRepository $venteRepository, EntityManagerInterface $entityManager, ClientRepository $clientRepository, PeintureRepository $peintureRepository)
    {
        $this->venteRepository = $venteRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->peintureRepository = $peintureRepository;
    }

    #[Route('/api/ventes', name: 'ventes_get_all', methods: ['GET'])]
    public function getVentes(): Response
    {
        $ventes = $this->venteRepository->findAll();

        // Vérifier si des ventes ont été trouvées
        if (empty($ventes)) {
            return new Response('No ventes found', Response::HTTP_NOT_FOUND);
        }

        // Transformer les ventes en tableau associatif
        $ventesData = [];
        foreach ($ventes as $vente) {
            $ventesData[] = [
                'id' => $vente->getId(),
                'idClient' => $vente->getClient()->getId(),
                'idPeinture' => $vente->getPeinture()->getId(),
                'amount' => $vente->getAmount(),
                'status' => $vente->getStatus(),
            ];
        }

        // Retourner les données des ventes en JSON
        return $this->json($ventesData);
    }

    #[Route('/api/ventes/{id}', name: 'vente_get', methods: ['GET'])]
    public function getVente(int $id): Response
    {
        $vente = $this->venteRepository->find($id);

        // Vérifier si la vente existe
        if (!$vente) {
            return new Response('Vente not found', Response::HTTP_NOT_FOUND);
        }

        // Transformer la vente en tableau associatif
        $venteData = [
            'id' => $vente->getId(),
            'idClient' => $vente->getClient()->getId(),
            'idPeinture' => $vente->getPeinture()->getId(),
            'amount' => $vente->getAmount(),
            'status' => $vente->getStatus(),
        ];

        // Retourner les données de la vente en JSON
        return $this->json($venteData);
    }

    #[Route('/api/ventes', name: 'vente_add', methods: ['POST'])]
    public function venteAdd(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        // Création d'une nouvelle instance de Vente
        $vente = new Vente();

        if ($data['idClient']){
            $client = $this->clientRepository->find($data['idClient']);
            if ($client){
                $vente->setClient($client);
            }
            else{
                return new Response('Client not found', Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            return new Response('Peinture not found', Response::HTTP_BAD_REQUEST);
        }

        if ($data['idPeinture']){
            $peinture = $this->peintureRepository->find($data['idPeinture']);
            if ($peinture){
                $vente->setPeinture($peinture);
            }
            else{
                return new Response('Peinture not found', Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            return new Response('Peinture not found', Response::HTTP_BAD_REQUEST);
        }

        $vente->setAmount($data['amount']);
        $vente->setStatus($data['status']);

        // Validation de l'entité Vente
        $errors = $validator->validate($vente);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        // Persister et flusher l'entité Vente
        $this->entityManager->persist($vente);
        $this->entityManager->flush();

        return new Response('Vente registered successfully', Response::HTTP_CREATED);
    }
}
