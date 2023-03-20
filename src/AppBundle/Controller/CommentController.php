<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Media;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Post;
use AppBundle\Entity\Review;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Helpers\DateHelper;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Comment controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Creates a new Comment entity.
     *
     * @Route("/new/{post}", name="comment_new")
     * @Method({"GET", "POST"})
     * @param Post $post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Post $post, Request $request)
    {
        $comment = new Comment();

        $comment->setPost($post);
        $comment->setText($request->query->get('text'));
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_ajax_new', ['post' => $post->getId()])
        ]);

        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setPost($post);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Post')->configureConnection();

            if($post->getUser() != $this->getUser()){
                $notify = new Notification();
                $notify->setUser($post->getUser());
                $notify->setType('comment');
                $notify->setData([
                    'post_id' => $post->getId(),
                    'user_id' => $this->getUser()->getId(),
                    'user_name' => $this->getUser()->getName(),
                ]);
                $em->persist($notify);
            }

            $em->persist($comment);
            $em->flush();
            if (is_null($post->getTradeland())) {
                return $this->redirectToRoute('post_index', ['user' => $post->getUser()->getId()]);
            } else {
                return $this->redirectToRoute('tradeland_show', ['tradeland' => $post->getTradeland()->getId()]);
            }
        }

        return $this->render('AppBundle:Comment:new.html.twig', [
            'comment' => $comment,
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new Comment entity via ajax.
     *
     * @Route("/ajax-new/{post}", name="comment_ajax_new")
     * @Method({"GET", "POST"})
     * @param Post $post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajaxNewAction(Post $post, Request $request)
    {
        $comment = new Comment();

        $comment->setPost($post);
        $comment->setText($request->query->get('text'));
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_ajax_new', ['post' => $post->getId()])
        ]);

        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setPost($post);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Post')->configureConnection();

            if($post->getUser() != $this->getUser()){
                $notify = new Notification();
                $notify->setUser($post->getUser());
                $notify->setType('comment');
                $notify->setData([
                    'post_id' => $post->getId(),
                    'user_id' => $this->getUser()->getId()
                ]);
                $em->persist($notify);
            }

            $em->persist($comment);
            $em->flush();

            $session = $request->getSession();

            $media_files = $session->get('media') ?: array();

            foreach ($media_files as $media_file) {

                $media = $em->getRepository('AppBundle:Media')->findByName($this->getUser()->getId(), $media_file[0]);

                if($media == null) {
        			$is_image = in_array(substr($media_file[0], strrpos($media_file[0], ".") + 1), array('jpg', 'jpeg', 'png', 'gif'));
                	$media = new Media();
                	$media->setName($media_file[0]);
                	$media->setOriginalName($media_file[1]);
                	$media->setTypeId($is_image ? 1 : 2);
                	$media->setUser($this->getUser());
                }
                $media->setComment($comment);
                $em->persist($media);

                $comment->addMedia($media);
            }
            $em->flush();

            $session->remove('media');

            $response = $this->render('AppBundle:Comment:_item.html.twig', [
                'user' => $user,
                'comment' => $comment
            ]);

            $response->headers->set('Content-Type', 'text/plain');

            return $response;
        }

		return new JsonResponse(['status' => 'FAIL']);
     }

    /**
     * Creates a new Comment entity via ajax for media.
     *
     * @Route("/media/ajax-new/", name="comment_media_ajax_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ajaxMediaNewAction(Request $request)
    {
        $comment = new Comment();

        if (strlen($request->get('appbundle_comment')['text']) > 0 
            && strlen($request->get('media_id')) > 0) {

        	$media_id = $request->get('media_id');

            $comment->setUser($this->getUser());
       		$comment->setMediaId($media_id);
       		$comment->setText($request->get('appbundle_comment')['text']);

            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Post')->configureConnection();

			$media = $em->getRepository('AppBundle:Media')->find($media_id);

            if($media->getUser() != $this->getUser()) {
                $notify = new Notification();
                $notify->setUser($media->getUser());
                $notify->setType('comment');
                $notify->setData([
                    'media_id' => $media_id,
                    'user_id' => $this->getUser()->getId()
                ]);
                $em->persist($notify);
            }

            $em->persist($comment);
            $em->flush();

            $session = $request->getSession();

            $media_files = $session->get('media') ?: array();

            foreach ($media_files as $media_file) {

                $media = $em->getRepository('AppBundle:Media')->findByName($this->getUser()->getId(), $media_file[0]);

                if($media == null) {
        			$is_image = in_array(substr($media_file[0], strrpos($media_file[0], ".") + 1), array('jpg', 'jpeg', 'png', 'gif'));
                	$media = new Media();
                	$media->setName($media_file[0]);
                	$media->setOriginalName($media_file[1]);
                	$media->setTypeId($is_image ? 1 : 2);
                	$media->setUser($this->getUser());
                }
                $media->setComment($comment);
                $em->persist($media);

                $comment->addMedia($media);
            }
            $em->flush();

            $session->remove('media');

        $media = $em->getRepository('AppBundle:Media')->find($request->get('media_id'));

        $post = $media->getPost();

        $helper = new DateHelper();

        if($post == null) {
        	$comment = $media->getComment();
        	$text = $comment->getText();
        	$media_time = $helper->time_elapsed_string($comment->getCreatedAt());
        } else {
        	$text = $post->getText();
        	$media_time = $helper->time_elapsed_string($post->getCreatedAt());
        }

    	$response = $this->render('AppBundle:Media:_post.html.twig', array(
    		'user' => $this->getUser(),
    		'text' => $text,
    		'comments' => $media->getComments()
    	));

            $response->headers->set('Content-Type', 'text/plain');

            return $response;
        }

		return new JsonResponse(['status' => 'FAIL']);
     }

    /**
     * Deletes a Comment entity.
     *
     * @Route("/delete", name="comment_remove")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		$table = $em->getRepository('AppBundle:Comment');
		$comment = $table->find($request->get('id'));

		if($comment->getUser()->getId() != \App::getInstance()->getUserId()) {
			return new JsonResponse(['status' => 'NOT MINE']);
		}

		$em->remove($comment);
		$em->flush();

		return new JsonResponse(['status' => 'SUCCESS']);
	}

    /**
     * Creates a new ArticleComment entity.
     *
     * @Route("/article_comment_new/{post}", name="article_comment_new")
     * @Method({"GET", "POST"})
     * @param Article $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newArticleCommentAction(Article $article, Request $request)
    {
        $comment = new Review();

        $comment->setArticle($article);
        $comment->setText($request->query->get('text'));
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('article_show', ['article' => $article->getId()]);
        }

        return $this->render('AppBundle:Comment:new.html.twig', [
            'comment' => $comment,
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Vote for a comment.
     *
     * @Route("/vote", name="comment_vote")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voteAction(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $table = $em->getRepository('AppBundle:Comment');

        if($request->get('id') != null) {

       		if(!$table->hasVote($user->getId(), $request->get('id'))) {
       			$table->addVote($user->getId(), $request->get('id'));

       			$em->getRepository('AppBundle:Notification')->add(
       				array(
       					$table->getOwnerId($request->get('id')),
       					"{\"user_id\":" . $user->getId() . ",\"comment_id\":" . $request->get('id') . "}",
       					'vote_comment'
       				)
       			);
       		}

       		$num_votes = $table->numVotes($request->get('id'));
 
       		return new JsonResponse(['status' => 'OK', 'votes' => $num_votes]);
        }

        return new JsonResponse(['status' => 'FAIL']);
    }
}
