<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin')]
#[Security('is_granted("ROLE_ADMIN")')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_article')]
    public function indexArticleAdmin(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/indexAdmin.html.twig', [
            'articles' => $articleRepository->findAllArticleAdminShort(),
            
        ]);
    }

    #[Route('/comment', name: 'app_admin_comment')]
    public function indexCommentAdmin(CommentRepository $commentRepository): Response
    {
        return $this->render('admin/commentAdmin.html.twig', [
            'comments' => $commentRepository->findAllCommentAdmin(),
        ]);
    }

    #[Route('/user', name: 'app_admin_user')]
    public function indexUserAdmin(UserRepository $userRepository): Response
    {
        return $this->render('admin/userAdmin.html.twig', [
            'users' => $userRepository->findAllUserAdmin(),
        ]);
    }

    #[Route('/edit_article/{id}', name: 'app_admin_article_edit')]
    public function editArticle(Request $request, ArticleRepository $articleRepository, int $id)
{
    $article = $articleRepository->findOneBy(['id' => $id]);
    $articleForm = $this->createForm(ArticleType::class, $article );
    $articleForm->handleRequest($request);

    if ($articleForm->isSubmitted() && $articleForm->isValid())
    {
        $date = new DateTime();
        $article->setUpdateDate($date);

        $file = $articleForm->get('img')->getData();
        if ($file) 
        {
            // Supprime l'ancien fichier d'image
            $oldFile = $article->getImg();
            if ($oldFile) 
            {
                $oldFilePath = $this->getParameter('miniature_directory') . '/' . $oldFile;
                if (file_exists($oldFilePath)) 
                {
                    unlink($oldFilePath);
                }
            }
            // Télécharge le nouveau fichier d'image
            $originalNameFile = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFileName = $originalNameFile.'_'.uniqid().'.'.$file->guessExtension();
            $article->setImg($newFileName);
            $file->move($this->getParameter('miniature_directory'),$newFileName);
        }

        $articleRepository->save($article, true);
        return $this->redirectToRoute('app_admin_article');
    }

    return $this->render('admin/editArticleAdmin.html.twig',[
        'form' => $articleForm->createView(),
        'article' => $article,
        'articleTitle' => $article->getTitle()
    ]);
}

    // #[Route('/', name: 'app_admin_user')]
    // public function indexUserAdmin(ArticleRepository $articleRepository): Response
    // {
    //     return $this->render('admin/index.html.twig', [
    //         'articles' => $articleRepository->findAllArticleAuthor(),
    //     ]);
    // }

    #[Route('/comment/{id}/moderation', name: 'app_comment_moderation_admin')]
    public function moderationCommentAdmin(CommentRepository $commentRepository, Comment $comment)
    {
        $comment->setCommentText('Commentaire retiré par la modération');
        $commentRepository->save($comment, true);

        return $this->redirectToRoute('app_article_show', ['id' => $comment->getArticle()->getId()]);
    }

    #[Route('/article/{id}/delete', name: 'app_article_delete')]
    public function deleteArticleAdmin(ArticleRepository $articleRepository, Article $article)
    {
        $user = $this->getUser();

        $articleRepository->remove($article, true);

        return $this->redirectToRoute('app_admin_article');
    }
}