<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function __construct($environment, $debug)
    {
        date_default_timezone_set( 'Canada/Eastern' );
        parent::__construct($environment, $debug);

        \App::getInstance()->setRootDir($this->getRootDir());
    }

    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),

            new FOS\UserBundle\FOSUserBundle(),
            new Http\HttplugBundle\HttplugBundle(), // If you require the php-http/httplug-bundle package.
			new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            //new APinnecke\Bundle\OAuthBundle\APinneckeOAuthBundle(),

            new Liip\ImagineBundle\LiipImagineBundle(),
        ];

        if (in_array($this->getEnvironment(), ['test'], true)) {
            // These are the other bundles the SonataAdminBundle relies on
        	$bundles[] = new Sonata\CoreBundle\SonataCoreBundle();
        	$bundles[] = new Sonata\AdminBundle\SonataAdminBundle();
        	$bundles[] = new Sonata\BlockBundle\SonataBlockBundle();
        	$bundles[] = new Knp\Bundle\MenuBundle\KnpMenuBundle();

        	// And finally, the storage and SonataAdminBundle
        	$bundles[] = new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle();
        	$bundles[] = new FOS\JsRoutingBundle\FOSJsRoutingBundle();
        }

        if (in_array($this->getEnvironment(), ['dev'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    	$loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
