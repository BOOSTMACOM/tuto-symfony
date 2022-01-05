<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/admin/blog", name="admin_blog")
     */
    public function index(): Response
    {
        $articles = $this->doctrine->getRepository(Article::class)->findAll();

        return $this->render('admin/blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/admin/blog/edit/{id}", name="admin_blog_edit")
     */
    public function edit(Article $article, Request $request): Response
    {

        // Construction du Formulaire grâce au Builder
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $slugger = new AsciiSlugger();
            $title = $article->getTitle();
            $article->setSlug(strtolower($slugger->slug($title)));

            //$doctrine->getManager()->persist($article);
            $this->doctrine->getManager()->flush();
        }

        return $this->render('admin/blog/edit.html.twig',[
            'form' => $form->createView() // on genère une "Vue" du formulaire pour l'affichage dans la Vue
        ]);
    }

    /**
     * @Route("/admin/blog/new", name="admin_blog_new")
     */
    public function new(Request $request): Response
    {

        // Construction du Formulaire grâce au Builder
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $slugger = new AsciiSlugger();
            $title = $article->getTitle();
            $article->setSlug(strtolower($slugger->slug($title)));
            $article->setPublishedAt(new \DateTime());

            $this->doctrine->getManager()->persist($article);
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('admin_blog');
        }

        return $this->render('admin/blog/new.html.twig',[
            'form' => $form->createView() // on genère une "Vue" du formulaire pour l'affichage dans la Vue
        ]);
    }

     /**
     * @Route("/admin/blog/delete/{id}", name="admin_blog_delete")
     */
    public function delete(Article $article)
    {
        $this->doctrine->getManager()->remove($article);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('admin_blog');
    }
}
