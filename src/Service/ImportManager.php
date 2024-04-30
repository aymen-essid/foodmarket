<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Supplier;
use App\Repository\ImportRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class ImportManager
{
    public function __construct(
        private string $targetDirectory,
        private ProductRepository $productRepo,
        private EntityManagerInterface $em
    ) 
    {}

   
    public function import(array $data, Supplier $supplier)
    {
        if($data){

            foreach($data as $productLine){

                $productExist = $this->productRepo->findOneByCode($productLine[1]);

                if(!$productExist instanceof Product)
                {
                    $product = new Product();
                    $product->setCode($productLine[1]);
                    $product->setDescription($productLine[0]);
                    $product->setPrice($productLine[2]);
                    $product->setSupplier($supplier);
                    $product->setCreatedAt(new DateTimeImmutable());

                    $this->em->persist($product);
                }
                else 
                {
                    $productExist->setCode($productLine[1]);
                    $productExist->setDescription($productLine[0]);
                    $productExist->setPrice($productLine[2]);
                    $productExist->setSupplier($supplier);
                    $productExist->setUpdatedAt(new DateTimeImmutable());

                    $this->em->persist($productExist);
                }       
            }
            $this->em->flush();

        }

    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}