<?php

namespace AppBundle\Controller;

//use AppBundle\Form\PasswordType;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\User;
use AppBundle\Entity\Provider;
use AppBundle\Entity\Experience;
use AppBundle\Form\UserType;
use AppBundle\Form\ExperienceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{

	/**
	 * Get User username by id.
	 *
	 * @Route("/info", name="user_info")
	 * @Method({"POST"})
	 * @param $request
	 * @return JsonResponse
	 */
	public function infoAction(Request $request)
	{
		$client = \Yiin\RocketChat\Client::getInstance();

		$result = $client->usersAPI()->info(
			'userId',
			$request->get('id')
		);

		return new JsonResponse(['username' => $result->user->username, 'name' => $result->user->name]);
	}

	/**
	 * Get User presense.
	 *
	 * @Route("/presense", name="user_presense")
	 * @Method({"POST"})
	 * @param $request
	 * @return JsonResponse
	 */
	public function presenseAction(Request $request)
	{
		$client = \Yiin\RocketChat\Client::getInstance();

		$result = $client->usersAPI()->getPresence(
			'username',
			'u' . $request->get('id')
		);

		return new JsonResponse(['status' => $result->presence]);
	}

	/**
	 * Show user's avatar.
	 *
	 * @Route("/avatar/{user}", name="avatar_show")
	 * @Method({"GET", "POST"})
	 * @param $user
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function avatarAction($user)
	{
		$em = $this->getDoctrine()->getManager();

		if(substr($user, 0, 1) == "u") {
			$user = substr($user, 1);
		}		
		$avatar = $em->getRepository('AppBundle:User')->getAvatar($user);

		if(strlen($avatar) > 0) {
			return $this->redirect('/bundles/framework/images/user/' . $avatar);
		} else {
			return $this->redirect('/bundles/framework/images/no_avatar.png');
		}
	}

    /**
     * Show User dashboard.
     *
     * @Route("/{user}", name="user_show", requirements={ * "user": "\d+" * })
     * @Method({"GET", "POST"})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(User $user)
    {
//      dump($user->isEnabled()); die();
        $em = $this->getDoctrine()->getManager();
//
        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($user->getId());
        $count = count($connections);
//        $ids = [$user];
//        foreach ($connections as $connection) {
//            if (!in_array($connection->getUser(), $ids)) {
//                $ids[] = $connection->getUser();
//            }
//
//            if (in_array($connection->getFromUser(), $ids)) {
//                continue;
//            }
//            $ids[] = $connection->getFromUser();
//        }

        $client = \Yiin\RocketChat\Client::getInstance();

        try {

        	$result = $client->usersAPI()->getPresence(
        		'username',
        		'u' . $user->getId()
        	);

        	$user->setStatus(
        		$result->presence
        	);

        } catch(\Exception $x) {

        	if($x->getCode() == 400) {

        		if($admin_client == null) {
        			$admin_client = new \Yiin\RocketChat\Client();
        			$result = $admin_client->loginAs(
        					'admin',
        					'Maui73shake'
        			);
        		}

                try {
	        		$admin_client->usersAPI()->create(
	        			"u" . $user->getId(),
	        			$user->getSalt(),
	        			strlen(trim($user->getName())) == 0 ? substr($user->getEmail(), 0, strpos($user->getEmail(), '@')) : $user->getName(),
	        			$user->getEmail(),
	        			array(
	        				'joinDefaultChannels' => false
	        			)
 	       			);
                } catch(\Exception $x) {}

        		$user->setStatus(
        				'offline'
        		);
        	}
        }

      	return $this->render('AppBundle:User:profile.html.php', array(
        	'user' => $user,
        	'experiences' => $user->getExperiences(),
     		'count' => $count
        ));
    }

    /**
     * Deletes user's experience record.
     *
     * @Route("/experience/delete", name="user_delete_experience")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteExperienceAction(Request $request)
    {
        $user = $this->getUser();
        $experience = new Experience();

        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Experience')->find($request->get('id'));

        if($record->getUser()->getId() != $user->getId()) {
        	throw $this->createAccessDeniedException('Access denied');
        }
        $em->remove($record);
        $em->flush();

        return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
    }

    /**
     * Displays a form to edit user's experience record.
     *
     * @Route("/experience/edit", name="user_edit_experience")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editExperienceAction(Request $request)
    {
        $user = $this->getUser();
        $experience = new Experience();

        $old_logo_file = null;

        if($request->get('id') != null) {
        	$experience = $this->getDoctrine()->getManager()->getRepository('AppBundle:Experience')->find($request->get('id'));
        	if($experience->getUser()->getId() != $user->getId()) {
        		throw $this->createAccessDeniedException('Access denied');
        	}

        	$old_logo_file = $experience->getLogo();
        }

        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);
        $errors = array();

        if ($form->isValid()) {
        	$record = $form->getData();
        	$record->setUser($user);

           	if ($record->getLogo() != null && $record->getLogo() instanceof UploadedFile) {
               	$fileName = $this->get('app.image_uploader')->uploadAndResize($record->getLogo(), $this->container->getParameter('company_directory'), 300, 300, 'fit');
				$record->setLogo($fileName);

				if($old_logo_file != null) {
					unlink($this->container->getParameter('company_directory') . "/" . $old_logo_file);
				}
            } else {
            	$record->setLogo($old_logo_file);
            }

            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Experience')->update(
            	$record
            );
            // $em->flush();

            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }

    	foreach ($form->getErrors() as $error) {
    		if($error->getCause() == null) {
    			continue;
    		}

     		$key = $error->getCause()->getPropertyPath();

    		$errors[substr($key, 5)] = $error->getCause()->getMessage();
    	}

        return $this->render('AppBundle:User:edit_experience.html.php', [
            'form' => $form->createView(),
            'experience' => $experience,
            'user' => $user,
            'errors' => $errors
        ]);
    }

    /**
     * Updates the avatar.
     *
     * @Route("/update-avatar", name="user_update_avatar")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAvatarAction(Request $request)
    {
    	$fileName = $this->get('app.image_uploader')->generateFileName(
    		$this->container->getParameter('avatar_directory'),
    		'png'
    	);

    	$tmp = fopen($this->container->getParameter('avatar_directory') . "/" . $fileName, "w");
    	fwrite($tmp, base64_decode($request->get('avatar')));
    	fclose($tmp);

    	$em = $this->getDoctrine()->getManager();

    	unlink($this->container->getParameter('avatar_directory') . "/" . $em->getRepository('AppBundle:User')->getAvatar(
    		$this->getUser()->getId()
    	));

    	$em->getRepository('AppBundle:User')->updateAvatar(
    		$this->getUser()->getId(),
    		$fileName
    	);
    	$em->flush();

    	return new JsonResponse(
    		['result' => 'SUCCESS', 'filename' => $fileName]
    	);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/edit", name="user_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $avatar = $user->getAvatar();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request); // ������������������ ���������� ��������������

        if ($form->isValid()) {

        	$record = $form->getData();

            $file = $user->getAvatar();
            if (!empty($file)) {
                $fileName = $this->get('app.image_uploader')->upload($file, $this->container->getParameter('avatar_directory'));
                $record->setAvatar($fileName);
/*
                $client = \Yiin\RocketChat\Client::getInstance($user);

                $fname = \App::getInstance()->getRootDir() . '/web/bundles/framework/images/user/' .  $user->getAvatar();

                $client->usersAPI()->setAvatarFile(
                	$fname
                );
*/
            } else {
                $record->setAvatar($avatar);
            }

            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:User')->update(
            	$record
            );
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Profile has been updated successfully');

            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }

        return $this->render('AppBundle:User:edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/password", name="password_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function passwordAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('password_edit'))
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])->getForm();

        /*   $form = $this->createForm(PasswordType::class, $user,[
                   'action' => $this->generateUrl('password_edit', ['id' => $user->getId()])
               ]
           );   */
        $form->handleRequest($request); // ������������������ ���������� ��������������

        if ($form->isValid()) {
            $password = $form["password"]->getData();
            $passwordEncoder = $this->container->get('security.password_encoder');
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Password has been updated successfully');

            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }

        if($form->isSubmitted()) {
        	return $this->render('AppBundle:User:password.html.twig', [
            	'user' => $user,
            	'password_form' => $form->createView(),
            	'form' => $this->createForm(UserType::class, $user)->createView()
        	]);
        } else {
        	return $this->render('AppBundle:User:_password.html.twig', [
            	'user' => $user,
            	'password_form' => $form->createView(),
        	]);
        }
    }

    /**
     * change  User status.
     *
     * @Route("/status/{status}", name="user_status")
     * @Method({"POST"})
     * @param $status
     * @return JsonResponse
     */
    public function StatusAction($status)
    {
//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
//        }

        $user = $this->getUser();
        $user->setStatus($status);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['status' => $status]);
    }

    /**
     * change  User status.
     *
     * @Route("/connect", name="user_connect")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function connectAction()
    {
        return $this->render('AppBundle:User:connect.html.twig');
    }

    /**
     * Instagram question.
     *
     * @Route("/instagram-question", name="user_instagram_question")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function instagramQuestionAction()
    {
    	$userInformation = $this->get('session')->get('instagram-profile-data');
        return $this->render('AppBundle:User:instagram-question.html.twig', [
            'userInformation' => $userInformation
        ]);
    }

    /**
     * Instagram question.
     *
     * @Route("/instagram-connect", name="user_instagram_connect")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function instagramConnectAction()
    {
    	$providerInformation = $this->get('session')->get('instagram-provider-data');
    	$provider_id = $providerInformation['provider_id'];
    	$accessToken = $providerInformation['token'];
    	$user = $this->get('security.token_storage')->getToken()->getUser();  

    	$em = $this->getDoctrine()->getManager();	               	

        if (null !== $provider = $em->getRepository('AppBundle:Provider')->findOneBy(['user' => $user->getId(), 'name' => 'instagram', 'provider_id' => $provider_id])) {
            $provider->setAccessToken($accessToken);
        } else {
            $provider = new Provider();
            $provider->setUser($user);
            $provider->setName('instagram');
            $provider->setProviderId($provider_id);
            $provider->setAccessToken($accessToken);
        }

        $em->persist($provider);
        $em->flush();
    	
    	header('Location: /user/connect'); die();
    }

    /**
     * Delete User social provider.
     *
     * @Route("/provider/{provider}", name="user_delete_provider", requirements={
     *     "provider": "google|twitter|facebook|instagram|vkontakte|foursquare|pinterest|flickr|imgur|odnoklassniki|tumblr"
     * })
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProviderAction($provider)
    {
        $item = $this->getUser()->getProviders()->matching(Criteria::create()->where(Criteria::expr()->eq("name", $provider)));
        if (isset($item[0])) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($item[0]);
            $em->flush();
        }

        return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
    }

    /**
     * Vote for a user.
     *
     * @Route("/vote", name="user_vote")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function voteAction(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $table = $em->getRepository('AppBundle:User');

        if($request->get('id') != null && $user->getId() != $request->get('id')) {
        	if(!$table->hasVote($user->getId(), $request->get('id'))) {
        		$table->addVote($user->getId(), $request->get('id'));

        		$em->getRepository('AppBundle:Notification')->add(
        			array(
        				$request->get('id'),
        				"{\"user_id\":" . $user->getId() . "}",
        				'vote_user'
        			)
        		);

        		$voted_user = $table->find($request->get('id'));

        		$template = $this->renderView(
                	'AppBundle:Email:user_voted.html.php',
                	array(
                		'user' => $user,
                		'voted_user' => $voted_user
                	)
                );

        		$slots = $this->get("templating.engine.php")['slots'];

            	$message = \Swift_Message::newInstance()
                	->setSubject($slots->get('subject'))
                	->setFrom($this->container->getParameter('fos_user.registration.confirmation.from_email'))
                	->setTo($voted_user->getEmail())
                	->setBody(
                		$slots->get('text_body')
                	)
                	->addPart(
                    	$template,
                    	'text/html'
                	);

            	$this->get('mailer')->send($message);
        	}

        	$num_votes = $table->numVotes($request->get('id'));

        	$users = $table->listVoted($request->get('id'));
 
        	return new JsonResponse(['status' => 'OK', 'votes' => $num_votes, 'users' => $users]);
        }

        return new JsonResponse(['status' => 'FAIL']);
    }


    /**
     * Unvote for a user.
     *
     * @Route("/unvote", name="user_unvote")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function unvoteAction(Request $request)
    {
    	$user = $this->getUser();

    	$em = $this->getDoctrine()->getManager();
    	$table = $em->getRepository('AppBundle:User');

    	if($request->get('id') != null && $user->getId() != $request->get('id')) {
    		if($table->hasVote($user->getId(), $request->get('id'))) {
    			$table->removeVote($user->getId(), $request->get('id'));
     		}

    		$num_votes = $table->numVotes($request->get('id'));
    		
    		$users = $table->listVoted($request->get('id'));

    		return new JsonResponse(['status' => 'OK', 'votes' => $num_votes, 'users' => $users]);
    	}

    	return new JsonResponse(['status' => 'FAIL']);
    }

}
