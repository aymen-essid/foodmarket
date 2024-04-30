<?php

namespace App\Controller;

use App\Entity\Import;
use App\Entity\Supplier;
use App\Form\ImportFormType;
use App\Service\FileParser;
use App\Service\FileUploader;
use App\Service\ImportManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request,EntityManagerInterface $entityManager, FileUploader $uploader, FileParser $fileParser, ImportManager $importManager): Response
    {

        $formImport = $this->createForm(ImportFormType::class);
        $formImport->handleRequest($request);

        if ($formImport->isSubmitted() && $formImport->isValid()) {

            $file = $formImport->get('importFile')->getData();
  
            $supplier = $formImport->get('suppliers')->getData();

            

            $newFilename = $uploader->upload($file);
            
            if ($newFilename) {
                // upload csv file
                $import = new Import();
                $import->setFileName($newFilename); 
                $import->setCreatedAt(new DateTimeImmutable());
                $entityManager->persist($import);           
                $entityManager->flush();

                // parse csv uploaded file
                $data = $fileParser->parseCSV($newFilename);

                // lunch import
                $importManager->import($data, $supplier); 
            

                
                $this->addFlash(
                    'success',
                    'Votre fichier CSV à été téléchargé avec succès'
                );
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'formImport' => $formImport,
        ]);
    }
}
