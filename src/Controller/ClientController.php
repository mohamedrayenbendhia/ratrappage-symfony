<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\User;
use App\Form\ProfileType;
use App\Form\RatingType;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/home', name: 'app_client_home', methods: ['GET'])]
    public function home(): Response
    {
        // Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Données statiques pour les maisons à louer
        $rentProperties = [
            [
                'id' => 1,
                'title' => 'Modern Apartment in City Center',
                'price' => 1200,
                'type' => 'Apartment',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'area' => 85,
                'location' => 'Downtown, Tunis',
                'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400',
                'description' => 'Beautiful modern apartment with stunning city views, fully furnished with modern amenities.'
            ],
            [
                'id' => 2,
                'title' => 'Cozy Villa with Garden',
                'price' => 2500,
                'type' => 'Villa',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 250,
                'location' => 'Sidi Bou Said',
                'image' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=400',
                'description' => 'Spacious villa with private garden, perfect for families. Quiet neighborhood with easy access to amenities.'
            ],
            [
                'id' => 3,
                'title' => 'Studio Near University',
                'price' => 600,
                'type' => 'Studio',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area' => 35,
                'location' => 'Manouba',
                'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400',
                'description' => 'Perfect for students, close to university campus with all necessary amenities included.'
            ],
            [
                'id' => 4,
                'title' => 'Luxury Penthouse',
                'price' => 3500,
                'type' => 'Penthouse',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 180,
                'location' => 'La Marsa',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400',
                'description' => 'Luxurious penthouse with panoramic sea views, rooftop terrace, and premium finishes.'
            ]
        ];

        // Données statiques pour les maisons à vendre
        $saleProperties = [
            [
                'id' => 5,
                'title' => 'Family House with Pool',
                'price' => 280000,
                'type' => 'House',
                'bedrooms' => 5,
                'bathrooms' => 3,
                'area' => 320,
                'location' => 'Carthage',
                'image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400',
                'description' => 'Magnificent family house with swimming pool, large garden, and modern interior design.'
            ],
            [
                'id' => 6,
                'title' => 'Investment Apartment',
                'price' => 150000,
                'type' => 'Apartment',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area' => 120,
                'location' => 'Ariana',
                'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400',
                'description' => 'Great investment opportunity in a growing area with excellent public transport connections.'
            ],
            [
                'id' => 7,
                'title' => 'Beachfront Villa',
                'price' => 450000,
                'type' => 'Villa',
                'bedrooms' => 6,
                'bathrooms' => 4,
                'area' => 400,
                'location' => 'Gammarth',
                'image' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=400',
                'description' => 'Exclusive beachfront villa with direct beach access, private dock, and luxury amenities.'
            ],
            [
                'id' => 8,
                'title' => 'Modern Townhouse',
                'price' => 220000,
                'type' => 'Townhouse',
                'bedrooms' => 4,
                'bathrooms' => 2,
                'area' => 200,
                'location' => 'Ben Arous',
                'image' => 'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?w=400',
                'description' => 'Contemporary townhouse in secure residential complex with community facilities.'
            ]
        ];

        return $this->render('client/home.html.twig', [
            'rentProperties' => $rentProperties,
            'saleProperties' => $saleProperties,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/dashboard', name: 'app_client_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(RatingRepository $ratingRepository): Response
    {
        // S'assurer que seuls les vrais clients (pas admin/super_admin) accèdent ici
        $user = $this->getUser();
        $roles = $user->getRoles();
        
        // Si c'est un admin ou super admin, rediriger vers leur interface
        if (in_array('ROLE_SUPER_ADMIN', $roles) || in_array('ROLE_ADMIN', $roles)) {
            return $this->redirectToRoute('app_user_index');
        }
        // Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        // Statistiques pour le dashboard
        $ratingsReceived = $ratingRepository->findRatingsForUser($user);
        $ratingsGiven = $ratingRepository->findRatingsByRater($user);
        $averageRating = $ratingRepository->getAverageRating($user);
        $totalRatingsReceived = $ratingRepository->countRatingsForUser($user);
        
        return $this->render('client/dashboard.html.twig', [
            'user' => $user,
            'ratingsReceived' => array_slice($ratingsReceived, 0, 5), // Les 5 derniers
            'ratingsGiven' => array_slice($ratingsGiven, 0, 5), // Les 5 derniers
            'averageRating' => $averageRating,
            'totalRatingsReceived' => $totalRatingsReceived,
            'totalRatingsGiven' => count($ratingsGiven),
        ]);
    }

    #[Route('/rating', name: 'app_client_rating')]
    public function rating(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        RatingRepository $ratingRepository
    ): Response {
        $user = $this->getUser();
        
        // Récupérer seulement les clients (pas les admins) sauf l'utilisateur actuel
        $availableUsers = $userRepository->findClientsExceptCurrent($user);
        
        $rating = new Rating();
        $rating->setRater($user);
        
        $form = $this->createForm(RatingType::class, $rating, [
            'available_users' => $availableUsers
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $ratedUser = $rating->getRated();
            
            // Vérifier si l'utilisateur a déjà noté cet utilisateur
            if ($ratingRepository->hasUserRated($user, $ratedUser)) {
                $this->addFlash('error', 'You have already rated this user.');
            } else {
                $entityManager->persist($rating);
                $entityManager->flush();
                
                $this->addFlash('success', 'Rating submitted successfully!');
                return $this->redirectToRoute('app_client_rating');
            }
        }
        
        // Récupérer les avis donnés par l'utilisateur
        $myRatings = $ratingRepository->findRatingsByRater($user);
        
        return $this->render('client/rating.html.twig', [
            'form' => $form->createView(),
            'myRatings' => $myRatings,
        ]);
    }

    #[Route('/ratings-received', name: 'app_client_ratings_received')]
    public function ratingsReceived(RatingRepository $ratingRepository): Response
    {
        $user = $this->getUser();
        $ratingsReceived = $ratingRepository->findRatingsForUser($user);
        $averageRating = $ratingRepository->getAverageRating($user);
        $totalRatings = $ratingRepository->countRatingsForUser($user);
        
        return $this->render('client/ratings_received.html.twig', [
            'ratingsReceived' => $ratingsReceived,
            'averageRating' => $averageRating,
            'totalRatings' => $totalRatings,
        ]);
    }

    #[Route('/profile', name: 'app_client_profile')]
    public function profile(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $uploadsDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/profile';
                    if (!is_dir($uploadsDirectory)) {
                        mkdir($uploadsDirectory, 0755, true);
                    }
                    $imageFile->move($uploadsDirectory, $newFilename);
                    $user->setImage('/uploads/profile/'.$newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading image: ' . $e->getMessage());
                }
            }

            // Gestion du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully!');

            return $this->redirectToRoute('app_client_profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/rating/{id}/delete', name: 'app_client_rating_delete', methods: ['POST'])]
    public function deleteRating(Rating $rating, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Vérifier que c'est bien l'auteur de l'avis
        if ($rating->getRater() !== $user) {
            $this->addFlash('error', 'You can only delete your own ratings.');
            return $this->redirectToRoute('app_client_rating');
        }
        
        $entityManager->remove($rating);
        $entityManager->flush();
        
        $this->addFlash('success', 'Rating deleted successfully!');
        return $this->redirectToRoute('app_client_rating');
    }
}
