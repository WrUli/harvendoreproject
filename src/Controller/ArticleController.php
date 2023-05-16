<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Comment;
use App\Entity\Vote;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ArticleController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function indexBlog(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllArticleAuthor(),
        ]);
    }

    #[Route('admin/new_article', name:'app_article_new', methods:['GET', 'POST'])]
    public function newArticle(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $article->setCreateDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {   
            $file = $form->get('img')->getData();
            if ($file) 
            {
                $originalNameFile = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $originalNameFile.'_'.uniqid().'.'.$file->guessExtension();
                $article->setImg($newFileName);
                $file->move($this->getParameter('miniature_directory'),$newFileName);
            }
            $articleRepository->save($article, true);
            return $this->redirectToRoute('app_blog', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('article/new.html.twig', [
            'form' => $form
        ]);
    }
    
    #[Route('article/{id}', name: 'app_article_show')]
    public function showArticleById(Request $request, ArticleRepository $articleRepository, CommentRepository $commentRepository,int $id)
    {
        $article = $articleRepository->findOneBy(['id' => $id]);
        $user = $this->getUser();
        
        $comments = $commentRepository->findBy(['article' => $article], ['createDate' => 'DESC']);
        $comment = new Comment();
        $comment->setCreateDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) 
        {
            $comment->setUser($user);
            $comment->setArticle($article);
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_article_show', ['id' => $id]);
        }
        
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView(),
            'comments' => $comments,
        ]);
    }

    #[Route('article/vote/new_vote', name: 'app_article_vote')]
    public function ratingArticle( Request $request, VoteRepository $voteRepository, ArticleRepository $articleRepository)
    {
        $value = $request->request->get('value');
        $articleId = $request->request->get('idArticle');
        $userId = $this->getUser();
        $lastVote = $voteRepository->findOneBy(['user' => $userId, 'article' => $articleId ]);
        $article = $articleRepository->find($articleId);

        if($lastVote) {
            if($lastVote->getType() === "like" && $value === "dislike"){
                $lastVote->setType('dislike');
                $article->setLikes($article->getLikes() -1 );
                $article->setDislikes($article->getDislikes() +1);
            }elseif ($lastVote->getType() === 'dislike' && $value === 'like') {
                $lastVote->setType('like');
                $article->setDislikes($article->getDislikes() - 1);
                $article->setLikes($article->getLikes() + 1);
            }
        }else {
            $vote = new Vote();
            $vote->setType($value);
            $vote->setArticle($article);
            $vote->setUser($this->getUser());

            if ($value === 'like') {
                $article->setLikes($article->getLikes() + 1);
            } elseif ($value === 'dislike') {
                $article->setDislikes($article->getDislikes() + 1);
            }
            $voteRepository->save($vote, true);
        }
        $articleRepository->save($article, true);
        return $this->redirectToRoute('app_article_show', ['id' => $articleId]);
    }
    
}