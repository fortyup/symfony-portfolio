<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/post', name: 'app_post')]
    public function index(PostRepository $postRepository, Request $request, PaginatorInterface $paginator)
    {
        // Paginate the results of the query
        $pagination = $paginator->paginate(
        // Doctrine Query, not results
            $postRepository->paginationQuery(),
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        // Affichage de la vue
        return $this->render('post/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/post/{id}', name: 'app_show')]
    public function show($id)
    {
        // Récupérez l'article depuis la base de données en fonction de l'ID
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->find($id);

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
