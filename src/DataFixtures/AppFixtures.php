<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        // Crear usuario freelancer
        $user1 = new User();
        $user1->setEmail('freelancer@example.com');
        $user1->setUsername('Freelancer Pro');
        $user1->setRoles(['ROLE_FREELANCER']);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, '123456'));
        $manager->persist($user1);

        // Crear usuario cliente
        $user2 = new User();
        $user2->setEmail('cliente@example.com');
        $user2->setUsername('Cliente Feliz');
        $user2->setRoles(['ROLE_CLIENT']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, '123456'));
        $manager->persist($user2);

        // Crear categorías
        $design = new Category();
        $design->setName('Diseño');
        $manager->persist($design);

        $writing = new Category();
        $writing->setName('Redacción');
        $manager->persist($writing);

        $dev = new Category();
        $dev->setName('Desarrollo Web');
        $manager->persist($dev);

        // Crear servicios
        $service1 = new Service();
        $service1->setTitle('Diseño de página web moderna');
        $service1->setDescription('Diseñaré una página moderna y profesional.');
        $service1->setPrice(200);
        $service1->setDeliveryTime(7);
        $service1->setCategory($design);
        $service1->setUser($user1);
        $manager->persist($service1);

        $service2 = new Service();
        $service2->setTitle('Redacción de artículo para blog');
        $service2->setDescription('Escribiré un artículo para mi blog sobre el tema actual.');
        $service2->setPrice(150);
        $service2->setDeliveryTime(5);
        $service2->setCategory($dev);
        $service2->setUser($user1);
        $manager->persist($service2);

        $manager->flush();
    }
}

