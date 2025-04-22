<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\CategoryRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/service', name: 'api_services_')]
class ServiceController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ServiceRepository $serviceRepository): JsonResponse
    {
        $services = $serviceRepository->findAll();

        $data = [];
        foreach ($services as $service) {
            $data[] = [
                'id' => $service->getId(),
                'title' => $service->getTitle(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice(),
                'deliveryTime' => $service->getDeliveryTime(),
                'category' => $service->getCategory()->getName(),
                'user' => $service->getUser()->getUsername(),
            ];
        }

        return $this->json($data);
    }

    #[Route('', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepo, UserRepository $userRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category = $categoryRepo->find($data['category']);
        $user = $userRepo->find($data['user']);

        if (!$category || !$user) {
            return $this->json(['error' => 'Categoría o usuario no válidos'], 400);
        }

        $service = new Service();
        $service->setTitle($data['title']);
        $service->setDescription($data['description']);
        $service->setPrice($data['price']);
        $service->setDeliveryTime($data['deliveryTime']);
        $service->setCategory($category);
        $service->setUser($user);

        $em->persist($service);
        $em->flush();

        return $this->json(['message' => 'Servicio creado con éxito', 'id' => $service->getId()], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Service $service): JsonResponse
    {
        $data = [
            'id' => $service->getId(),
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
            'price' => $service->getPrice(),
            'deliveryTime' => $service->getDeliveryTime(),
            'category' => $service->getCategory()->getName(),
            'user' => $service->getUser()->getUsername(),
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Service $service, EntityManagerInterface $em, CategoryRepository $categoryRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) $service->setTitle($data['title']);
        if (isset($data['description'])) $service->setDescription($data['description']);
        if (isset($data['price'])) $service->setPrice($data['price']);
        if (isset($data['deliveryTime'])) $service->setDeliveryTime($data['deliveryTime']);
        if (isset($data['category'])) {
            $category = $categoryRepo->find($data['category']);
            if ($category) $service->setCategory($category);
        }

        $em->flush();

        return $this->json(['message' => 'Servicio actualizado']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Service $service, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($service);
        $em->flush();

        return $this->json(['message' => 'Servicio eliminado']);
    }
}

