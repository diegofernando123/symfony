<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\User;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;

use Symfony\Component\Security\Core\Security;

use AppBundle\Form\SignupForm;
use AppBundle\Form\LoginForm;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /*$em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByEmail('pdandreyv@gmail.com');

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
        /*
        $password = 'Troica123';
            $passwordEncoder = $this->container->get('security.password_encoder');
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $em->persist($user);
            $em->flush();
        
        var_dump($user->getId()); exit;*/
        if (!is_null($this->getUser())) {
            return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
        }

        return $this->render('index.html.twig');
    }

    /**
     * @Route("/register/", name="register")
     */
    public function registerAction(Request $request)
    {
    	$form = new SignupForm();

    	$form = $this->createForm(get_class($form), $form, $form->getDefaultOptions(array()));

    	if($request->isMethod(Request::METHOD_POST)) {
    		$form->handleRequest($request);
    		if($form->isSubmitted() && $form->isValid()) {
    			$data = $form->getData();

    			$user = new User();
    			$user->setName($data['first_name'] . " " . $data['last_name']);
    			$user->setEmail($data['email']);
    			$user->setEnabled(0);
    			$user->setPlainPassword($data['password']);

			$user_form = $this->container->get("fos_user.registration.form.factory")->createForm();
        		$user_form->setData($user);

               		$event = new FormEvent($user_form, $request);
                	$this->container->get("event_dispatcher")->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

    			$em = $this->getDoctrine()->getManager();
    			$em->persist($user);
    			$em->flush();

                	if (null === $response = $event->getResponse()) {
                    		$url = $this->generateUrl('user_connect');
                    		$response = new RedirectResponse($url);
                	}

    			return $response;
    		}
    	}

    	$errors = array();

    	foreach ($form->getErrors() as $error) {
    		
    		$key = $error->getCause()->getPropertyPath();
    		
    		$errors[substr($key, 5, strlen($key) - 6)] = $error->getCause()->getMessage();
    	}
 
     	return $this->render('AppBundle:Default:register.html.php', array(
        	'form' => $form->createView(),
     		'errors' => $errors
        ));
    }

    protected function signin($user, $request, $response = null) {

    	$session = $request->getSession();

    	$session->remove(Security::AUTHENTICATION_ERROR);
    	$session->remove(Security::LAST_USERNAME);

    	$securityContext = $this->container->get("security.token_storage");
    	//    	$authenticationManager = $this->container->get("security.authentication.manager");

    	$authenticatedToken = new UsernamePasswordToken(
    			$user,
    			$user->getPassword(),
    			"secured_area",
    			$user->getRoles()
    	);

    	$securityContext->setToken($authenticatedToken);
    	$session->set(Security::LAST_USERNAME, $user->getEmail());
    	$session->set("_security_secured_area", serialize($authenticatedToken));

// 		UserTable::getInstance()->update_login_time($user->getId());

//    	if(!$request->cookies->has('remember_me') && $response != null) {
//    		$key = RememberKeyTable::getInstance()->generateKey($user->getId());
//    		$response->headers->setCookie(new Cookie('remember_me', $key, mktime(date('H'), date('i'), date('s'), date('n'), date('j'), date('Y') + 20)));
//    	}
    }
 
    /**
     * @Route("/login/", name="login")
     */
    public function loginAction(Request $request)
    {
    	$form = new LoginForm();

    	$form = $this->createForm(get_class($form), $form, $form->getDefaultOptions(array()));

    	if($request->isMethod(Request::METHOD_POST)) {
    		$form->handleRequest($request);
    		if($form->isSubmitted() && $form->isValid()) {
    			$data = $form->getData();

    			$em = $this->getDoctrine()->getManager();
    			$user = $em->getRepository('AppBundle:User')->findOneByEmail($data['email']);

    			$this->signin($user, $request);

    			if(in_array("ADMIN", $user->getRoles()) && \App::getInstance()->getContainer()->getParameter('kernel.environment') == "test") {
    				return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    			}

    			return $this->redirect($this->generateUrl('user_connect'));
    		}
    	}

    	$errors = array();

    	foreach ($form->getErrors() as $error) {
    
    		$key = $error->getCause()->getPropertyPath();
    
    		$errors[substr($key, 5, strlen($key) - 6)] = $error->getCause()->getMessage();
    	}

    	return $this->render('AppBundle:Default:login.html.php', array(
    			'form' => $form->createView(),
    			'errors' => $errors
    	));
    }
    
    /**
     * @Route("/payment", name="payment_index")
     */
    public function paymentAction()
    {
        return $this->render('AppBundle:Payment:index.html.twig');
    }

    /**
     * @Route("/test", name="test_index")
     */
    public function testAction()
    {
        return $this->render('AppBundle:Company:_form.html.twig');
    }

    /**
     * upload Image to the company.
     *
     * @Route("/company_image", name="company_image")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {

//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
//        }

        $form = $this->createFormBuilder()
            ->add('logo', FileType::class, [
                'constraints' => new Image(['mimeTypes' => ["image/jpg", "image/jpeg", "image/gif", "image/png"]])
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $name = $this->get('app.image_uploader')->upload($form['logo']->getData(), $this->container->getParameter('company_directory'));
            return new JsonResponse(['success' => $name]);
        }
        return $this->render('AppBundle:Company:image.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * upload Image to the company.
     *
     * @Route("/video", name="video")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadVideo(Request $request)
    {
//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
//        }
        //   return new JsonResponse(['success' => $_FILES]);
        $videoForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('post_new'))
            ->add('video', FileType::class)->getForm();

        $videoForm->handleRequest($request);
        if (!$request->isXmlHttpRequest() or !$request->isMethod($request::METHOD_POST)) {
            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
        }

        $name = basename($_FILES['video']['name']);
        $uploadfile = $this->container->getParameter('post_video_directory') . '/' . basename($_FILES['video']['name']);

        if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadfile)) {
            return new JsonResponse(['success' => $name]);
        } else {
            return new JsonResponse(['error' => '������������������ ���������� �� �������������� ���������������� ����������������!']);
        }
    }


    /**
     * @Route("/place/autocomplete", name="place_autocomplete")
     */
    public function placeAutocompleteAction(Request $request)
    {
    	if($this->getUser() == null) {
    		throw $this->createAccessDeniedException('Access denied');
    	}

    	$params = array(
    		'input' => $request->get('term'),
    		'types' => 'geocode',
    		'language' => 'en',
    		'key' => 'AIzaSyDg25wDF_75wfkDR_ngrMiO5Yj1oyLqy0U'
    	);

    	$param = "";

    	foreach($params as $key => $value) {
    		if(strlen($param) > 0) {
    			$param .= "&";
    		}
    		$param .= urlencode($key) . "=" . urlencode($value);
    	}

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 0);

			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    			'Connection: Keep-Alive',
    			'Keep-Alive: 300'
			));	
			curl_setopt($curl, CURLOPT_TIMEOUT, 7200);
//			curl_setopt($curl, CURLOPT_SSLVERSION,3);
//			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			// 30k buffer size
			curl_setopt($curl, CURLOPT_BUFFERSIZE, 1024 * 30);	
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($curl, CURLOPT_VERBOSE, true); 

		curl_setopt($curl, CURLOPT_URL, "https://maps.googleapis.com/maps/api/place/autocomplete/json?" . $param);

		$res = curl_exec($curl);

		$results = json_decode($res, true);

		$output = array();

		foreach($results['predictions'] as $prediction) {
			$output[] = array(
				'id' => $prediction['description'],
				'label' => $prediction['description'],
				'value' => $prediction['description']
			);
		}

		curl_exec($curl);

		return new JsonResponse($output);    	
    }

}
