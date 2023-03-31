<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends AbstractController
{

    #[Route('/blog', name: 'app_blog')]
    public function indexBlog(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllArticleAuthor(),
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete')]
    public function deleteComment(CommentRepository $commentRepository, Comment $comment)
    {
        $user = $this->getUser();

        // Vérifiez que l'utilisateur est l'auteur du commentaire
        if ($comment->getUser() != $user) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire');
        }

        $commentRepository->remove($comment, true);

        return $this->redirectToRoute('app_article_show', ['id' => $comment->getArticle()->getId()]);
    }

    #[Route('/comment/{id}/edit', name: 'app_comment_edit')]
    public function editComment(CommentRepository $commentRepository, Comment $comment)
    {   
        $user = $this->getUser();

        if ($comment->getUser() != $user) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire');
        }
        $date = new DateTime();
        $comment->setUpdateDate($date);
        $commentRepository->save($comment, true);

        return $this->redirectToRoute('app_article_show', ['id' => $comment->getArticle()->getId()]);
        
    }
}


