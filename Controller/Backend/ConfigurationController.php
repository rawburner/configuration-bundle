<?php

namespace ConfigurationBundle\Controller\Backend;

use ConfigurationBundle\Entity\Configuration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigurationController
 * @package ConfigurationBundle\Backend\Controller
 */
class ConfigurationController extends Controller
{

    /**
     * @Route("/configuration/list", name="configuration_list")
     * @param Request $request
     * @return Response
     */
    public function configurationAction(Request $request)
    {
        if($request->request->has('conf_value')){
            foreach ($request->request->get('conf_value') as $id => $conf){
                $configuration = $this->getDoctrine()->getRepository(Configuration::class)->find($id);
                if(!$configuration){
                    continue;
                }
                $configuration->setValue($request->request->get('conf_value')[$id]);
                $this->getDoctrine()->getManager()->persist($configuration);
            }
            $this->addFlash('notice', 'Konfiguration aktualisiert');
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('@Configuration/Backend/configuration.html.twig');
    }


    /**
     * @Route("/configuration/delete/{id}", name="configuration_delete")
     * @ParamConverter("configuration", class="ConfigurationBundle:Configuration")
     * @param $configuration Configuration
     * @return RedirectResponse
     */
    public function deleteConfigurationAction($configuration){
        $this->getDoctrine()->getManager()->remove($configuration);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('notice', 'Konfiguration '.$configuration->getName().' entfernt');
        return $this->redirectToRoute('configuration_list');
    }

    /**
     * @Route("/configuration/add", name="configuration_add")
     * @param Request $request
     * @return RedirectResponse
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
            $this->getDoctrine()->getManager()->persist($configuration);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', 'Konfiguration '.$configuration->getName().' erstellt');
        }
        return $this->redirectToRoute('configuration_list');
    }

}
