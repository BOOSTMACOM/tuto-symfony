<?php

namespace App\Controller;

use App\Entity\Article;
use App\DTOs\BlogSearchDTO;
use App\Form\BlogSearchType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {

        if($request && $request->get('blog_search'))
        {
            $articles = $articleRepository->findBySearch($request->get('blog_search')['search']);
        }
        else
        {
            $articles = $articleRepository->findBy([],[
                'id' => 'DESC'
            ]);
        }

        //$articles = $articleRepository->findByTitleBeginWithTop();

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/{slug}", name="blog_show")
     */
    public function show(Article $article): Response
    {
        // SELECT * FROM article WHERE slug = {slug}
        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]); 
    }

    public function searchForm()
    {
        $form = $this->createForm(BlogSearchType::class, null, [
            'action' => $this->generateUrl('blog'),
            'method' => 'GET'
        ]);
        return $this->render('blog/search_form.html.twig',[
            'form' => $form->createView()
        ]);
    }


}
