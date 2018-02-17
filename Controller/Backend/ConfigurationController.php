<?php

namespace ConfigurationBundle\Controller\Backend;

use ConfigurationBundle\Entity\Configuration;
use ConfigurationBundle\Entity\ConfigurationRepository;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

/**
 * Class ConfigurationController
 * @package ConfigurationBundle\Backend\Controller
 * @Route(service="config.controller")
 */
class ConfigurationController
{
    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var \Twig_Environment
     */
    private $twig_Environment;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ConfigurationController constructor.
     * @param ConfigurationRepository $configurationRepository
     * @param Router $router
     * @param Session $session
     * @param \Twig_Environment $twig_Environment
     * @param EntityManager $entityManager
     */
    public function __construct(
        ConfigurationRepository $configurationRepository,
        Router $router,
        Session $session,
        \Twig_Environment $twig_Environment,
        EntityManager $entityManager
    )
    {
        $this->configurationRepository = $configurationRepository;
        $this->router = $router;
        $this->session = $session;
        $this->twig_Environment = $twig_Environment;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/configuration/list", name="configuration_list")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function configurationAction(Request $request)
    {
        if($request->request->has('conf_value')){
            foreach ($request->request->get('conf_value') as $id => $conf){
                $configuration = $this->configurationRepository->find($id);
                if(!$configuration){
                    continue;
                }
                $configuration->setValue($request->request->get('conf_value')[$id]);
                $this->entityManager->persist($configuration);
            }
            $this->session->getFlashBag()->add('notice', 'Konfiguration aktualisiert');
            $this->entityManager->flush();
        }

        // replace this example code with whatever you need
        $output = $this->twig_Environment->render('@Configuration/Backend/configuration.html.twig');
        return new Response($output);
    }


    /**
     * @Route("/configuration/delete/{id}", name="configuration_delete")
     * @ParamConverter("configuration", class="ConfigurationBundle:Configuration")
     * @param $configuration Configuration
     * @return RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteConfigurationAction($configuration){
        $this->entityManager->remove($configuration);
        $this->entityManager->flush();
        $this->session->getFlashBag()->add('notice', 'Konfiguration '.$configuration->getName().' entfernt');
        $route = $this->router->generate('configuration_list');
        return new RedirectResponse($route);
    }

    /**
     * @Route("/configuration/add", name="configuration_add")
     * @param Request $request
     * @return RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addConfigurationAction(Request $request)
    {
        if($request->request->has('conf_name')){
            $configuration = new Configuration();
            $configuration->setName($request->request->get('conf_name'));
            $configuration->setValue($request->request->get('conf_newvalue'));
            $configuration->setType($request->request->get('conf_type'));
            $configuration->setOptions($request->request->get('conf_options'));
            $configuration->setPublic($request->request->get('conf_public', 0));
            $configuration->setGroup($request->request->get('conf_group'));
            $this->entityManager->persist($configuration);
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('notice', 'Konfiguration '.$configuration->getName().' erstellt');
        }
        $route = $this->router->generate('configuration_list');
        return new RedirectResponse($route);
    }

}