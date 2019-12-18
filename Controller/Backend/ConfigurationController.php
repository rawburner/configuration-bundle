<?php

namespace ConfigurationBundle\Controller\Backend;

use ConfigurationBundle\Entity\Configuration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Alexander Keil (alexanderkeil@leik-software.com)
 */
class ConfigurationController implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @Route("/configuration/list", name="configuration_list")
     */
    public function configurationAction(Request $request): Response
    {
        if($request->request->has('conf_value')){
            foreach ($request->request->get('conf_value') as $id => $conf){
                $configuration = $this->container->get('doctrine.orm.entity_manager')->getRepository(Configuration::class)->find($id);
                if(!$configuration){
                    continue;
                }
                $configuration->setValue($request->request->get('conf_value')[$id]);
                $this->container->get('doctrine.orm.entity_manager')->persist($configuration);
            }
            $this->container->get('session')->getFlashBag()->add('notice', 'Konfiguration aktualisiert');
            $this->container->get('doctrine.orm.entity_manager')->flush();
        }

        return new Response($this->container->get('twig')->render('@Configuration/Backend/configuration.html.twig'));
    }


    /**
     * @Route("/configuration/delete/{id}", name="configuration_delete")
     * @ParamConverter("configuration", class="ConfigurationBundle:Configuration")
     */
    public function deleteConfigurationAction(Configuration $configuration): Response
    {
        $this->container->get('doctrine.orm.entity_manager')->remove($configuration);
        $this->container->get('doctrine.orm.entity_manager')->flush();
        $this->container->get('session')->getFlashBag()->add('notice', 'Konfiguration '.$configuration->getName().' entfernt');
        return $this->redirectToRoute('configuration_list');
    }

    /**
     * @Route("/configuration/add", name="configuration_add")
     */
    public function addConfigurationAction(Request $request): Response
    {
        if($request->request->has('conf_name')){
            $configuration = new Configuration();
            $configuration->setName($request->request->get('conf_name'));
            $configuration->setValue($request->request->get('conf_newvalue'));
            $configuration->setType($request->request->get('conf_type'));
            $configuration->setOptions($request->request->get('conf_options'));
            $configuration->setPublic($request->request->get('conf_public', 0));
            $configuration->setGroup($request->request->get('conf_group'));
            $this->container->get('doctrine.orm.entity_manager')->persist($configuration);
            $this->container->get('doctrine.orm.entity_manager')->flush();
            $this->container->get('session')->getFlashBag()->add('notice', 'Konfiguration '.$configuration->getName().' erstellt');
        }
        return $this->redirectToRoute('configuration_list');
    }

    protected function redirectToRoute(string $routename): RedirectResponse
    {
        $route = $this->container->get('router')->generate($routename);
        return new RedirectResponse($route);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
