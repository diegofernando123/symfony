<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Company controller.
 *
 * @Route("/message")
 */
class MessageController extends Controller
{
    /**
     * Lists all Message entities.
     *
     * @Route("/", name="message_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
    	$user = $this->getUser();

    	if($user == null) {
          	return $this->redirectToRoute('login');
    	}

    	$me = 'u' . $user->getId();

        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('AppBundle:Network')->getConnections($this->getUser()->getId());

        $client = \Yiin\RocketChat\Client::getInstance($user);

        $admin_client = null;

/*
        $result = $client->loginAs(
        		'admin',
        		'Maui73shake'
        );
*/

        foreach($contacts as &$contact) {
        	try {

        		$result = $client->usersAPI()->getPresence(
        				'username',
        				'u' . $contact['id']
        		);

        		$contact['presense'] = $result->presence;

        	} catch(\Exception $x) {

        		if($x->getCode() == 400) {

        			if($admin_client == null) {
        				$admin_client = new \Yiin\RocketChat\Client();
        				$result = $admin_client->loginAs(
        						'admin',
        						'Maui73shake'
        				);
        			}

        			if($contact['salt'] == null || strlen($contact['salt']) == 0) {
        				$user = $em->getRepository('AppBundle:User')->find($contact['id']);
       					$contact['salt'] = $user->getSalt();
        			}

        			$admin_client->usersAPI()->create(
        					"u" . $contact['id'],
        					$contact['salt'],
        					strlen(trim($contact['name'])) == 0 ? substr($contact['email'], 0, strpos($contact['email'], '@')) : $contact['name'],
        					$contact['email'],
        					array(
        							'joinDefaultChannels' => false
        					)
        			);
/*
        			if(strlen($contact['avatar']) > 0) {
        				$fname = \App::getInstance()->getRootDir() . '/web/bundles/framework/images/user/' .  $contact['avatar'];

        				$admin_client->usersAPI()->setAvatarFile(
        					$fname
        				);
        			}
*/
        			$contact['presense'] = "offline";
        		}
        	}
        }

        $uinfo = array();

        $clientvatars = array();
        $clientvatars[$me] = $user->getAvatar();

        foreach($contacts as &$contact) {
        	$clientvatars['u' . $contact['id']] = $contact['avatar'];
        	$uinfo['u' . $contact['id']] = $contact;
        }

        $history = $client->imAPI()->lists()->ims;

        foreach($history as $key => &$record) {
        	if(!isset($record->lastMessage)) {
        		unset($history[$key]);
        		continue;
        	}

        	foreach($record->usernames as $user) {
        		if($user !== $me && $user !== 'admin') {

        			if(!isset($uinfo[$user])) {
        				$uinfo[$user] = $em->getRepository('AppBundle:User')->lookup(substr($user, 1));

        				try {

        					$result = $client->usersAPI()->getPresence(
        						'username',
        						$user
        					);

        				} catch(\Exception $x) {}

        				$uinfo[$user]['presense'] = $result->presence;
        			}

        			$record->user = $uinfo[$user];
         		}
        	}
        }

        $active_tab = $request->get("tab", "contacts");
        $active_user = $request->get("user");

        return $this->render('AppBundle:Message:index.html.php', [
        	'contacts' => $contacts,
        	'active_tab' => $active_tab,
        	'active_user' => $active_user,
        	'active_menu' => 'messages',
        	'avatars' => $clientvatars,
        	'history' => $history,
        	'authtoken' => $client->auth->authToken
        ]);
    }


    /**
     * Show unread messages in header.
     *
     * @Route("/header", name="message_header")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerAction()
    {
    	$user = $this->getUser();

    	$client = \Yiin\RocketChat\Client::getInstance($user);

    	$history = $client->imAPI()->lists()->ims;
    	$subscriptions = $client->subscriptionsAPI()->get();

    	foreach($history as &$record) {
    		$history[$record->_id] = $record->lastMessage;
    	}

    	$unread = 0;

    	foreach($subscriptions->update as $key => &$record) {
    		if($record->unread > 0) {
    			$unread += $record->unread;
    			$record->lastMessage = $history[$record->rid];
    		} else {
    			unset($subscriptions->update[$key]);
    		}
    	}

    	return $this->render('AppBundle:Message:header.html.php', [
    			'messages' => $subscriptions->update,
    			'total_unread' => $unread,
    	]);
    }
    
    
    /**
     * Mark the message as read.
     *
     * @Route("/markAsRead", name="message_mark_as_read")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function markAsReadAction(Request $request)
    {
    	$user = $this->getUser();

    	$client = \Yiin\RocketChat\Client::getInstance($user);
    	$subscriptions = $client->subscriptionsAPI()->markAsRead($request->get('rid'));

    	return new JsonResponse(['success' => 1]);
    }
}
