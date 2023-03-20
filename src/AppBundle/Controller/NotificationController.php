<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Notification controller.
 *
 * @Route("/notification")
 */
class NotificationController extends Controller
{
    /**
     * @Route("/", name="notification_index")
     */
    public function indexAction()
    {
        $notifications = $this->getUser()->getNotifications();

        $accepted = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "accepted")));
        $pending = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "pending")));
        $ignored = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "ignored")));
        $vote_users = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "vote_user")));
        $vote_posts = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "vote_post")));
        $vote_companies = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "vote_company")));
        $comments = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "comment")));

        $em = $this->getDoctrine()->getManager();
        $user_table = $em->getRepository('AppBundle:User');
        $network_table = $em->getRepository('AppBundle:Network');

        foreach($ignored as $key => &$record) {
        	$data = $record->getData();
        	$user = $user_table->find($data['user_id']);
        	if($user == null) {
        		$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($ignored[$key]);
        		continue;
        	}
         	$data['user_name'] = $user->getName();
         	$data['user_avatar'] = $user->getAvatar();
         	$record->setData($data);
        }

        foreach($pending as $key => &$record) {
        	$data = $record->getData();
        	$user = $user_table->find($data['user_id']);
        	if($user == null) {
        		$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($pending[$key]);
        		continue;
        	}
        	$connection = $network_table->find($data['connection_id']);
        	if($connection == null) {
        		//$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($pending[$key]);
        		continue;
        	}
         	$data['user_name'] = $user->getName();
         	$data['user_avatar'] = $user->getAvatar();
         	$record->setData($data);
        }

        foreach($accepted as $key => &$record) {
        	$data = $record->getData();
        	$user = $user_table->find($data['user_id']);
         	if($user == null) {
         		$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($accepted[$key]);
        		continue;
        	}
         	$data['user_name'] = $user->getName();
         	$data['user_avatar'] = $user->getAvatar();
         	$record->setData($data);
        }

        foreach($vote_users as $key => &$record) {
        	$data = $record->getData();
        	$user = $user_table->find($data['user_id']);
        	if($user == null) {
        		$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($vote_users[$key]);
        		continue;
        	}
         	$data['user_name'] = $user->getName();
         	$data['user_avatar'] = $user->getAvatar();
         	$record->setData($data);
        }

        foreach($vote_posts as $key => &$record) {
        	$data = $record->getData();
        	$user = $user_table->find($data['user_id']);
         	if($user == null) {
        		$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($vote_posts[$key]);
        		continue;
        	}
         	$data['user_name'] = $user->getName();
         	$data['user_avatar'] = $user->getAvatar();
         	$record->setData($data);
        }

        foreach($vote_companies as $key => &$record) {
        	$data = $record->getData();
        	$user = $user_table->find($data['user_id']);
        	if($user == null) {
        		$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($vote_companies[$key]);
        		continue;
        	}
         	$data['user_name'] = $user->getName();
         	$data['user_avatar'] = $user->getAvatar();
         	$record->setData($data);
        }

        foreach($comments as $key => &$record) {
        	$data = $record->getData();
        	$user = $user_table->find($data['user_id']);
        	if($user == null) {
        		$em->getRepository('AppBundle:Notification')->remove($record->getId());
        		unset($comments[$key]);
        		continue;
        	}
         	$data['user_name'] = $user->getName();
         	$data['user_avatar'] = $user->getAvatar();
         	$record->setData($data);
        }

        $notes = new ArrayCollection(
            array_merge($vote_users->toArray(), $vote_posts->toArray(), $vote_companies->toArray(), $ignored->toArray(), $comments->toArray())
        );

        return $this->render('AppBundle:Notification:index.html.twig', [
            'count' => $notifications->count(),
            'accepted' => $accepted,
            'pending' => $pending,
            'notes' => $notes,
        ]);
    }


}
