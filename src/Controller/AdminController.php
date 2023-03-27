<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin')]
#[Security('is_granted("ROLE_ADMIN")')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/comment/{id}/moderation', name: 'app_comment_moderation_admin')]
    public function deleteCommentAdmin(CommentRepository $commentRepository, Comment $comment)
    {
        $comment->setCommentText('Commentaire retiré par la modération');
        $commentRepository->save($comment, true);

        return $this->redirectToRoute('app_article_show', ['id' => $comment->getArticle()->getId()]);
    }

    
}
