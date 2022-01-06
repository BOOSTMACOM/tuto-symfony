<?php

namespace App\Controller\Admin;

use App\DTOs\EditArticleDTO;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Services\ArticleManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/blog")
 */
class BlogController extends AbstractController
{
    private ArticleManagerService $articleManager;

    public function __construct(ArticleManagerService $articleManager)
    {
        $this->articleManager = $articleManager;
    }

    /**
     * @Route("/", name="admin_blog_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/blog/index.html.twig', [
            'articles' => $articleRepository->findBy([], [
                'id' => 'DESC'
            ]),
        ]);
    }

    /**
     * @Route("/new", name="admin_blog_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $article = new EditArticleDTO();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->insert($article);

            return $this->redirectToRoute('admin_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_blog_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('admin/blog/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_blog_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id): Response
    {
        $dto = $this->articleManager->createDtoFromEntity($id);
        $form = $this->createForm(ArticleType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->update($dto);

            return $this->redirectToRoute('admin_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/blog/edit.html.twig', [
            'article' => $dto,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_blog_delete", methods={"POST"})
     */
    public function delete(Request $request, int $id): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $this->articleManager->delete($id);
        }

        return $this->redirectToRoute('admin_blog_index', [], Response::HTTP_SEE_OTHER);
    }
}
