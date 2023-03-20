<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Network;
use AppBundle\Entity\Notification;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Network controller.
 *
 * @Route("/network")
 */
class NetworkController extends Controller
{
    /**
     * Lists all Users with connection status.
     * @Route("/", name="network_index")
     */
    public function networkAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAllWithoutUser($this->getUser()->getId());
        $connections = $em->getRepository('AppBundle:Network')->findAllUserConnections($this->getUser()->getId());

        $ids = $statuses = [];
        foreach ($connections as $connection) {
            if (!in_array($connection->getUser(), $ids) and $connection->getUser() != $this->getUser()) {
                $ids[] = $connection->getUser();
                $statuses[$connection->getStatus()][$connection->getUser()->getId()] = $connection->getId();
            }

            if (in_array($connection->getFromUser(), $ids) or $connection->getFromUser() === $this->getUser()) {
                continue;
            }
            $ids[] = $connection->getFromUser();
            $statuses[$connection->getStatus()][$connection->getFromUser()->getId()] = $connection->getId();
        }

        return $this->render('AppBundle:Network:index.html.twig', ['users' => $users, 'connections' => $ids, 'statuses' => $statuses]);
    }

    /**
     * @Route("/connect/{user}", name="network_connect")
     * @param User $user

     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectAction($user)
    {
        if ($user == $this->getUser()->getId()) {
            throw $this->createNotFoundException('Wrong parameters');
        }

        $em = $this->getDoctrine()->getManager();
        $networkTable = $em->getRepository('AppBundle:Network');

        if (!$networkTable->hasConnection($this->getUser()->getId(), $user)) {

        	$connection_id = $networkTable->addConnection(
        		array(
        			$this->getUser()->getId(),
        			$user,
        			'pending'
        		)
        	);

        	$em->getRepository('AppBundle:Notification')->add(
        		array(
        			$user,
        			"{\"connection_id\":" . $connection_id . ",\"user_id\":" . $this->getUser()->getId() . "}",
        			'pending'
        		)
        	);

            $response = 'Pending';

        } else {

            $connection = $networkTable->findConnection(
            	$this->getUser()->getId(),
            	$user
            );

        	$notes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findNetworkUserNotifications($this->getUser()->getId());
        	foreach ($notes as $note) {
            	if(isset($note->getData()['connection_id']) and $note->getData()['connection_id'] == $connection->getId()) {
                	$em->remove($note);
            	}
        	}

        	$notes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findNetworkUserNotifications($user);
        	foreach ($notes as $note) {
            	if(isset($note->getData()['connection_id']) and $note->getData()['connection_id'] == $connection->getId()) {
                	$em->remove($note);
            	}
        	}

            $networkTable->removeConnection(
            	$this->getUser()->getId(),
            	$user
            );
            $response = 'Connect';
        }
 
        return new JsonResponse(['message' => $response]);
    }

    /**
     * @Route("/unconnect/{connection}", name="network_unfriend")
     * @param Network $connection
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unfriendAction(Network $connection)
    {
        if ($connection->getFromUser() !== $this->getUser() and $connection->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Wrong parameters');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($connection);
        $em->flush();

        return $this->redirectToRoute('network_index');
    }

    /**
     * @Route("/status/{connection}/{status}", name="network_status")
     * @param Network $connection
     * @param  string $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function statusAction(Network $connection, $status)
    {
        if ($connection->getStatus() != 'pending' or !in_array($status, ['accepted', 'ignored']) or ($connection->getUser() != $this->getUser() and $connection->getFromUser() != $this->getUser())) {
            throw $this->createNotFoundException('Wrong parameters');
        }
        $connection->setStatus($status);
        $em = $this->getDoctrine()->getManager();

        $notes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findNetworkUserNotifications($connection->getUser());

        foreach ($notes as $note) {
            if(isset($note->getData()['connection_id']) and $note->getData()['connection_id'] == $connection->getId()) {
                $em->remove($note);
            }
        }

        $notes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findNetworkUserNotifications($connection->getFromUser());

        foreach ($notes as $note) {
            if(isset($note->getData()['connection_id']) and $note->getData()['connection_id'] == $connection->getId()) {
                $em->remove($note);
            }
        }

        if ($status == 'accepted') {
            $notify = new Notification();
            $notify->setUser($connection->getUser());
            $notify->setType('accepted');
            $notify->setData([
                'connection_id' => $connection->getId(),
                'user_id' => $connection->getFromUser()->getId()
            ]);
            $em->persist($notify);

            $this->get('session')->getFlashBag()->add('success', 'You accepted the request to become a friend from " . $connection->getUser()->getName() . "!');

        } elseif ($status == 'ignored') {
            $notify = new Notification();
            $notify->setUser($connection->getFromUser());
            $notify->setType('ignored');
            $notify->setData([]);
            $em->persist($notify);
            $em->remove($connection);

            $this->get('session')->getFlashBag()->add('success', 'You rejected the request to become a friend from " . $connection->getUser()->getName() . "!');
        }

        $em->flush();

        return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
    }

    /**
     * @Route("/connections", name="user_network")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectionsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($this->getUser()->getId());

        return $this->render('AppBundle:Network:connections.html.twig', ['connections' => $connections, 'user' => $this->getUser()]);
    }

    /**
     * @Route("/{user}/connections", name="user_connections")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userConnectionsAction($user)
    {
        $em = $this->getDoctrine()->getManager();

        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($user);

        return $this->render('AppBundle:Network:connections.html.twig', ['connections' => $connections, 'user' => $em->getRepository('AppBundle:User')->find($user)]);
    }

    /**
     * @Route("/invite", name="network_invite")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction()
    {
        $em = $this->getDoctrine()->getManager();

        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($this->getUser()->getId());

        return $this->render('AppBundle:Network:invite.html.twig', ['connections' => $connections]);
    }

    /**
     * Show Recently Friends
     *
     * @Route("/recently-friends", name="user_recently_friends")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recentlyFriendsAction(Request $request)
    {
     	$em = $this->getDoctrine()->getManager();

    	$records = $em->getRepository('AppBundle:Network')->findLatestFriends(
    		\App::getInstance()->getUserId()
    	);

    	return $this->render('AppBundle:Network:_recently_friends.html.php', array(
    		'records' => $records
    	));
    }

    /**
     * People You May Know
     *
     * @Route("/people-you-may-know", name="people_you_may_know")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function peopleYouMayKnowAction(Request $request)
    {
     	$em = $this->getDoctrine()->getManager();

    	$records = $em->getRepository('AppBundle:Network')->findPeopleYouMayKnow(
    		\App::getInstance()->getUserId()
    	);

    	return $this->render('AppBundle:Network:_people_you_may_know.html.php', array(
    		'records' => $records
    	));
    }

    /**
     * Show Online Friends
     *
     * @Route("/friends-online", name="user_online_friends")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function friendsOnlineAction(Request $request)
    {
     	$em = $this->getDoctrine()->getManager();

    	$records = $em->getRepository('AppBundle:Network')->findOnlineFriends(
    		\App::getInstance()->getUserId()
    	);

    	if(count($records) == 0) {
    		return new Response('');
    	}

    	return $this->render('AppBundle:Network:_online_friends.html.php', array(
    		'records' => $records
    	));
    }

}
