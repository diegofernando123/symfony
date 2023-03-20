<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Media;
use AppBundle\Helpers\DateHelper;

/**
 * Media controller.
 *
 * @Route("/media")
 */
class MediaController extends Controller
{
/*
    function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function generateRandomFileName($extension, $path) {

    	$len = 32;

   		while(true) {
   			$name = $this->generateRandomString($len++) . "." . $extension;

   			if(!file_exists($name)) {
   				return $name;
   			}
   		}

        return $randomString;
    }
*/
    /**
     * @Route("/remove", name="remove_media")
     * @param Request $request
     * @return JsonResponse
     */
    public function removeAction(Request $request)
    {
    	$session = $request->getSession();
    	$media_files = $session->get('media') ?: array();

        $image_directory = $this->container->getParameter('post_directory');
        $video_directory = $this->container->getParameter('post_video_directory');

    	$filename = $request->get('name');

        foreach ($media_files as $key => $media_file) {
        	if(strcmp($filename, $media_file[1]) == 0) {
        		$is_image = in_array(substr($media_file[0], strrpos($media_file[0], ".") + 1), array('jpg', 'jpeg', 'png', 'gif'));
        		$directory = $is_image ? $image_directory : $video_directory;

        		$em = $this->getDoctrine()->getManager();

               	$media = $em->getRepository('AppBundle:Media')->findByName($this->getUser()->getId(), $media_file[0]);

               	if($media != null) {
               		$em->remove($media);
               		$em->flush();
               	}

               	$media = $em->getRepository('AppBundle:Media')->findByName($this->getUser()->getId(), $media_file[0]);

        		if($media == null && file_exists($directory . "/" . $media_file[0])) {
        		 	unlink($directory . "/" . $media_file[0]);
        		}

        		unset($media_files[$key]);
        	}
        }

        $session->set('media', $media_files);

    	$result = array(
    		'result' => 'SUCCESS'
    	);

        return new JsonResponse($result);
    }

    /**
     * @Route("/details", name="details_media")
     * @param Request $request
     * @return JsonResponse
     */
    public function detailsAction(Request $request)
    {
    	$session = $request->getSession();

//        $image_directory = $this->container->getParameter('post_directory');
//        $video_directory = $this->container->getParameter('post_video_directory');

        $helper = new DateHelper();
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('AppBundle:Post')->configureConnection();
        $media = $em->getRepository('AppBundle:Media')->find($request->get('id'));

        $post = $media->getPost();

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

    	$result = array(
    		'result' => 'SUCCESS',
    		'data' => array(
    			'user' => array(
    				'id' => $media->getUser()->getId(),
    				'name' => $media->getUser()->getName(),
    				'avatar' => $media->getUser()->getAvatar()
    			),
    			'title' => $media->getOriginalName(),
    			'text' => $media->getOriginalName(),
    			'time' => $media_time,
    			'html' => $response->getContent(),
    			'votes' => $media->getNumVotes()
    		)
    	);

        return new JsonResponse($result);
    }

    /**
     * @Route("/upload", name="upload_media")
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadAction(Request $request)
    {
        $session = $request->getSession();
        $image_directory = $this->container->getParameter('post_directory');
        $video_directory = $this->container->getParameter('post_video_directory');

        $media_files = $session->get('media') ?: array();
        foreach ($request->files as $uploadedFile) {
        	$dzuuid = $request->get('dzuuid');
        	$found = false;
        	if(strlen($dzuuid) > 0) {
        		foreach($media_files as $key => $media_file) {
        			if(strcmp($media_file[2], $dzuuid) === 0) {
        				$found = true;

        				$is_image = in_array(substr($media_file[0], strrpos($media_file[0], ".") + 1), array('jpg', 'jpeg', 'png', 'gif'));
						$filename = ($is_image ? $image_directory : $video_directory) . "/" . $media_file[0];

        				$file = fopen($filename, "a");

        				fwrite($file, $uploadedFile->openFile()->fread($uploadedFile->getSize()));
        				fclose($file);

        				if($is_image && intval($request->get('dztotalchunkcount')) == (intval($request->get('dzchunkindex')) + 1)) {
        					$this->get('app.image_uploader')->resizeImage($filename, 1200, 1200);
        				}

         				if(intval($request->get('dztotalchunkcount')) == (intval($request->get('dzchunkindex')) + 1)) {

         					$em = $this->getDoctrine()->getManager();

                    		$media = new Media();
                    		$media->setName($media_file[0]);
                    		$media->setOriginalName($media_file[1]);
                    		$media->setTypeId($is_image ? 1 : 2);
                    		$media->setUser($this->getUser());
                    		$em->persist($media);

                    		$em->getRepository('AppBundle:Media')->updateFilename($this->getUser()->getId(), $media_file[0], $media_file[1], $is_image ? $image_directory : $video_directory);
                    		$em->flush();
                    	}
        			}
        		}
        	}

        	if(!$found) {
        		$is_image = in_array($uploadedFile->guessExtension(), array('jpg', 'jpeg', 'png', 'gif'));
        		if($is_image) {
        			if(strlen($dzuuid) > 0) {
        				$name = $this->get('app.image_uploader')->upload($uploadedFile, $image_directory);
        			} else {
        				$name = $this->get('app.image_uploader')->uploadAndResize($uploadedFile, $image_directory, 1200, 1200, 'fit');
					}
        		} else {
        			$name = $this->get('app.image_uploader')->upload($uploadedFile, $video_directory);
        		}

        		if(strlen($dzuuid) == 0) {
         			$em = $this->getDoctrine()->getManager();

                	$media = new Media();
                	$media->setName($name);
                	$media->setOriginalName($uploadedFile->getClientOriginalName());
                	$media->setTypeId($is_image ? 1 : 2);
                	$media->setUser($this->getUser());
                	$em->persist($media);

                	$em->getRepository('AppBundle:Media')->updateFilename($this->getUser()->getId(), $name, $uploadedFile->getClientOriginalName(), $is_image ? $image_directory : $video_directory);
                	$em->flush();
                }

        		$media_files[] = array(
        			$name,
        			$uploadedFile->getClientOriginalName(),
        			$dzuuid
        		);
        	}
        }

        $session->set('media', $media_files);

        return new JsonResponse($media_files);
    }

    /**
     * Vote for a media.
     *
     * @Route("/vote", name="media_vote")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voteAction(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $table = $em->getRepository('AppBundle:Media');

        if($request->get('id') != null) {

       		if(!$table->hasVote($user->getId(), $request->get('id'))) {
       			$table->addVote($user->getId(), $request->get('id'));

       			$em->getRepository('AppBundle:Notification')->add(
       				array(
       					$table->getOwnerId($request->get('id')),
       					"{\"user_id\":" . $user->getId() . ",\"media_id\":" . $request->get('id') . "}",
       					'vote_media'
       				)
       			);
       		}

       		$num_votes = $table->numVotes($request->get('id'));
 
       		return new JsonResponse(['status' => 'OK', 'votes' => $num_votes]);
        }

        return new JsonResponse(['status' => 'FAIL']);
    }
}
