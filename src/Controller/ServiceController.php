<?php

namespace App\Controller;


use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\CategoryRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
final class ServiceController extends AbstractController
{
    #[Route(name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }

    // Rutas para la API (JSON)
    
    #[Route('/api/services', name: 'app_service_index', methods: ['GET'])]
    public function apiIndex(ServiceRepository $serviceRepository): JsonResponse
    {
        $services = $serviceRepository->findAll();
        return $this->json($services, 200, [], ['groups' => 'service:read']);
    }

    #[Route('/api/services/new', name: 'api_services_create', methods: ['POST'])]
    public function apiCreate(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepo, UserRepository $userRepo): JsonResponse
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

    #[Route('/api/services/{id}', name: 'api_services_show', methods: ['GET'])]
    public function apiShow(Service $service): JsonResponse
    {
        return $this->json($service, 200, [], ['groups' => 'service:read']);
    }

    #[Route('/api/services/{id}', name: 'api_services_update', methods: ['PUT'])]
    public function apiUpdate(Request $request, Service $service, EntityManagerInterface $em, CategoryRepository $categoryRepo): JsonResponse
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

    #[Route('/api/services/{id}', name: 'api_services_delete', methods: ['DELETE'])]
    public function apiDelete(Service $service, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($service);
        $em->flush();

        return $this->json(['message' => 'Servicio eliminado']);
    }
}