<?php
// src/Controller/CertificateController.php
namespace App\Controller;

use App\Entity\Certificat;
use App\Entity\Peinture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Annotation\Route;


class CertificateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/generate-certificate', name: 'generate_certificat', methods: ['POST'])]
    public function generate(Request $request): Response
    {
        // Récupère les données JSON du corps de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifie que l'ID est bien défini
        if (!isset($data['id'])) {
            return new Response('ID not provided', 400);
        }

        // Récupère l'ID de la peinture depuis la requête
        $peintureId = $data['id'];

        // Récupère la peinture depuis la base de données
        $peinture = $this->entityManager->getRepository(Peinture::class)->find($peintureId);

        if (!$peinture) {
            return new Response('Painting not found', 404);
        }

        // Crée un nouvel objet Certificat
        $certificat = new Certificat();
        $certificat->setPeinture($peinture);
        // $certificat->setSignature('Signature Placeholder'); // Remplacer par la signature réelle

        // Persiste le certificat dans la base de données
        $this->entityManager->persist($certificat);
        $this->entityManager->flush();

        // Génère le HTML du certificat avec les données de la peinture et du certificat
        $html = $this->renderView('certificate/template.html.twig', [
            'title' => $peinture->getTitle(),
            // 'name' => $peinture->getName(),
            'dimensions' => $peinture->getWidth() . 'x' . $peinture->getHeight(),
            'date' => $peinture->getCreatedAt()->format('Y-m-d H:i:s'), // Utilise getCreatedAt() pour récupérer la date de création
            'medium' => $peinture->getMethod(),
            'id' => $certificat->getId(), // Utilise getId() pour récupérer l'ID du certificat
            'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
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
}