<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),

            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),
            new Stenik\ContentBundle\StenikContentBundle(),
            new Stenik\CoreBundle\StenikCoreBundle(),
            new Stenik\NewsBundle\StenikNewsBundle(),
            new Stenik\SettingsBundle\StenikSettingsBundle(),
            new Stenik\TinyMCEBundle\StenikTinyMCEBundle(),
            new Stenik\TranslationsBundle\StenikTranslationsBundle(),

            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new Widop\PhpBBBundle\WidopPhpBBBundle(),

            new Application\Stenik\NewsBundle\ApplicationStenikNewsBundle(),
            new Application\Stenik\ContentBundle\ApplicationStenikContentBundle(),
            new Application\Stenik\SliderBundle\ApplicationStenikSliderBundle(),

            // new Application\Stenik\TranslationsBundle\ApplicationStenikTranslationsBundle(),

            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),

            new Stenik\BackgroundsBundle\StenikBackgroundsBundle(),
            new Stenik\SliderBundle\StenikSliderBundle(),
            new Stenik\BannersBundle\StenikBannersBundle(),
            new Stenik\PublishWorkflowBundle\StenikPublishWorkflowBundle(),
            new Stenik\SEOBundle\StenikSEOBundle(),
            new Stenik\FrontendBundle\StenikFrontendBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
