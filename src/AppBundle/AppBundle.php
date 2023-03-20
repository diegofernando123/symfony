<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
	public function boot() {
		\App::getInstance()->init($this->container);

		$twig = $this->container->get('twig');
		$twig->addGlobal('application', \App::getInstance());

	}

	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
