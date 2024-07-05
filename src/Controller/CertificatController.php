<?php

namespace App\Controller;

use App\Entity\Certificat;
use App\Entity\Client;
use App\Entity\Peinture;
use App\Repository\CertificatRepository;
use App\Repository\ClientRepository;
use App\Service\MyCacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Annotation\Route;


class CertificatController extends AbstractController
{
    private $entityManager;
    private ClientRepository $clientRepository;
    private CertificatRepository $certificatRepository;

    public function __construct(EntityManagerInterface $entityManager, ClientRepository $clientRepository, CertificatRepository $certificatRepository)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->certificatRepository = $certificatRepository;
    }

    #[Route('/api/generate-certificate/{idPeinture}/{idClient}', name: 'generate_certificat', methods: ['GET'])]
    public function generate(int $idPeinture, int $idClient, Request $request): Response
    {
        // Vérifier si un certificat avec idPeinture et idClient existe déjà
        $existingCertificat = $this->entityManager->getRepository(Certificat::class)->findOneBy([
            'peinture' => $idPeinture,
            'client' => $idClient,
        ]);

        if ($existingCertificat) {
            // Si un certificat existe déjà, retourner directement le PDF correspondant
            return $this->generatePdfResponse($existingCertificat);
        }

        // Récupère la peinture depuis la base de données
        $peinture = $this->entityManager->getRepository(Peinture::class)->find($idPeinture);

        if (!$peinture) {
            return new Response('Peinture not found', 404);
        }

        // Crée un nouvel objet Certificat
        $certificat = new Certificat();
        $certificat->setPeinture($peinture);

        $clientUser = $this->clientRepository->find($idClient);
        $certificat->setClient($clientUser);

        // Persiste le certificat dans la base de données
        $this->entityManager->persist($certificat);
        $this->entityManager->flush();

        // Génère le PDF du certificat et retourne la réponse
        return $this->generatePdfResponse($certificat);
    }

    private function generatePdfResponse(Certificat $certificat): Response
    {
        $name = $certificat->getClient()->getFirstname() . ' ' . $certificat->getClient()->getLastname();

        // Génère le HTML du certificat avec les données du certificat
        $html = $this->renderView('certificate/template.html.twig', [
            'title' => $certificat->getPeinture()->getTitle(),
            'name' => $name,
            'dimensions' => $certificat->getPeinture()->getWidth() . 'x' . $certificat->getPeinture()->getHeight(),
            'date' => $certificat->getPeinture()->getCreatedAt()->format('d/m/Y'),
            'medium' => $certificat->getPeinture()->getMethod(),
            'id' => $certificat->getId(),
            'generated_at' => $certificat->getGeneratedAt()->format('d/m/Y à H:i:s'),
        ]);

        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Crée une instance de Dompdf
        $dompdf = new Dompdf($options);

        // Charge le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Définit la taille du papier et l'orientation
        $dompdf->setPaper(array(0, 0, 633, 494), 'portrait');

        // Rend le HTML en PDF
        $dompdf->render();

        // Renvoie la réponse avec le PDF en tant que contenu
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificat.pdf"'
        ]);
    }

    #[Route('/api/certificats', name: 'certificats_get_all', methods: ['GET'])]
    public function getCertificats(): Response
    {
        $certificats = $this->certificatRepository->findAll();

        // Vérifier si des peintures ont été trouvées
        if (empty($certificats)) {
            return new Response('No certificats found', Response::HTTP_NOT_FOUND);
        }

        // Transformer les peintures en tableau associatif
        $certificatsData = [];
        foreach ($certificats as $certificat) {
            $certificatsData[] = [
                'id' => $certificat->getId(),
                'idPeinture' => $certificat->getPeinture()->getId(),
                'idClient' => $certificat->getClient()->getId(),
            ];
        }

        // Retourner les données des peintures en JSON
        return $this->json($certificatsData);
    }

}