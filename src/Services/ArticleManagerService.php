<?php

namespace App\Services;

use App\DTOs\EditArticleDTO;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ArticleManagerService 
{
    private EntityManagerInterface $entityManager;
    private ArticleRepository $repo;

    public function __construct(EntityManagerInterface $entityManager, ArticleRepository $repo)
    {
        $this->entityManager = $entityManager;
        $this->repo = $repo;
    }

    public function createDtoFromEntity(int $id) : EditArticleDTO
    {
        $article = $this->repo->find($id);

        $dto = (new EditArticleDTO())
        ->setId($article->getId())
        ->setTitle($article->getTitle())
        ->setContent($article->getContent());

        return $dto;
    }

    public function insert(EditArticleDTO $dto)
    {
        $article = (new Article())
        ->setTitle($dto->getTitle())
        ->setContent($dto->getContent())
        ->setPublishedAt(new \DateTime());

        $this->entityManager->persist($this->slugify($article));
        $this->entityManager->flush();
    }

    public function update(EditArticleDTO $dto)
    {
        $article = $this->repo->find($dto->getId());
        $article
            ->setTitle($dto->getTitle())
            ->setContent($dto->getContent());
            
        $article = $this->slugify($article);
        $this->entityManager->flush();
    }

    public function delete(int $id)
    {
        $article = $this->repo->find($id);
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }

    public function slugify(Article $article)
    {
        $slugger = new AsciiSlugger();

        $title = $article->getTitle();
        $article->setSlug($slugger->slug($title));

        return $article;
    }

}