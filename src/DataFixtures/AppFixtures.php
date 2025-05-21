<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\Freelancer;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {

        // Crear dos usuarios
        $user1 = new User();
        $user1->setUsername('usuario1');
        $user1->setEmail('usuario1@email.com');
        $user1->setRoles(['ROLE_FREELANCER']);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, '123456'));
        $manager->persist($user1);

        // Crear perfil de freelancer para usuario1
        $freelancer = new Freelancer();
        $freelancer->setUserId($user1);
        $freelancer->setTitle('Desarrollador Web Full Stack');
        $freelancer->setDescription('Especialista en desarrollo web con más de 5 años de experiencia en tecnologías frontend y backend. Experto en PHP, JavaScript, React y Symfony.');
        $freelancer->setSkills(['PHP', 'JavaScript', 'React', 'Symfony', 'MySQL', 'Docker', 'Git']);
        $freelancer->setHourlyRate(35);
        $manager->persist($freelancer);

        $user2 = new User();
        $user2->setUsername('usuario2');
        $user2->setEmail('usuario2@email.com');
        $user2->setRoles(['ROLE_CLIENT']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, '123456'));
        $manager->persist($user2);

        // Crear una reseña del usuario2 hacia el usuario1 (freelancer)
        $review = new Review();
        $review->setUser($user2);
        $review->setFreelancer($freelancer);
        $review->setRating(5);
        $review->setComment('Excelente trabajo. El desarrollador entregó el proyecto antes de tiempo y con una calidad excepcional. Su conocimiento en React y Symfony es impresionante. Definitivamente volveré a contratarlo para futuros proyectos.');
        $manager->persist($review);
        
        // Crear una segunda reseña para tener más datos
        $review2 = new Review();
        $review2->setUser($user2);
        $review2->setFreelancer($freelancer);
        $review2->setRating(4);
        $review2->setComment('Muy buen trabajo en el desarrollo de mi sitio web. Resolvió todos los problemas técnicos rápidamente y la comunicación fue fluida. Recomendado para proyectos de desarrollo web.');
        $manager->persist($review2);

        // Crear un chat entre los dos usuarios
        $chat = new Chat();
        $chat->setUser1($user1);
        $chat->setUser2($user2);
        $manager->persist($chat);

        // Crear mensajes en el chat
        $message1 = new Message();
        $message1->setContent('¡Hola! ¿Cómo estás?');
        $message1->setDate(new \DateTime('-10 minutes'));
        $message1->setSender($user1);
        $message1->setReceiver($user2);
        $message1->setChat($chat);
        $manager->persist($message1);

        $message2 = new Message();
        $message2->setContent('¡Hola! Muy bien, ¿y tú?');
        $message2->setDate(new \DateTime('-8 minutes'));
        $message2->setSender($user2);
        $message2->setReceiver($user1);
        $message2->setChat($chat);
        $manager->persist($message2);

        $message3 = new Message();
        $message3->setContent('Todo bien, gracias por preguntar.');
        $message3->setDate(new \DateTime('-5 minutes'));
        $message3->setSender($user1);
        $message3->setReceiver($user2);
        $message3->setChat($chat);
        $manager->persist($message3);

        $categories = [
            [
                'name' => 'Desarrollo Web',
                'icon' => 'code',
                'description' => 'Sitios web, aplicaciones web y desarrollo de software personalizado',
            ],
            [
                'name' => 'Diseño y Creatividad',
                'icon' => 'design',
                'description' => 'Diseño gráfico, diseño UI/UX y trabajo creativo',
            ],
            [
                'name' => 'Redacción y Traducción',
                'icon' => 'writing',
                'description' => 'Redacción de contenido, copywriting y servicios de traducción',
            ],
            [
                'name' => 'Marketing y SEO',
                'icon' => 'marketing',
                'description' => 'Marketing digital, optimización SEO y publicidad',
            ],
            [
                'name' => 'Video y Animación',
                'icon' => 'video',
                'description' => 'Edición de video, gráficos en movimiento y servicios de animación',
            ],
            [
                'name' => 'Ciencia de Datos',
                'icon' => 'data',
                'description' => 'Análisis de datos, visualización y aprendizaje automático',
            ],
            // Categorías adicionales basadas en la imagen
            [
                'name' => 'Desarrollo Móvil',
                'icon' => 'mobile',
                'description' => 'iOS, Android y desarrollo de aplicaciones móviles multiplataforma',
            ],
            [
                'name' => 'Contabilidad y Finanzas',
                'icon' => 'accounting',
                'description' => 'Contabilidad, análisis financiero y preparación de impuestos',
            ],
            [
                'name' => 'Servicios Legales',
                'icon' => 'legal',
                'description' => 'Asesoramiento legal, revisión de contratos y cumplimiento normativo',
            ],
            [
                'name' => 'Atención al Cliente',
                'icon' => 'support',
                'description' => 'Servicio al cliente, soporte técnico y asistencia virtual',
            ],
            [
                'name' => 'Audio y Música',
                'icon' => 'audio',
                'description' => 'Locución, producción musical y edición de audio',
            ],
            [
                'name' => 'Ingeniería y Arquitectura',
                'icon' => 'engineering',
                'description' => 'Diseño CAD, ingeniería de productos y servicios arquitectónicos',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setIcon($categoryData['icon']);
            $category->setDescription($categoryData['description']);
            
            $manager->persist($category);
            $this->addReference('category_' . strtolower(str_replace(' ', '_', $categoryData['name'])), $category);
        }

        $manager->flush();
    }
}

