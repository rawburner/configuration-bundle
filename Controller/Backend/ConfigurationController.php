<?php

namespace ConfigurationBundle\Controller\Backend;

use ConfigurationBundle\Entity\Configuration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Alexander Keil (alexanderkeil@leik-software.com)
 */
class ConfigurationController extends AbstractController
{

    /**
     * @Route("/configuration/list", name="configuration_list")
     */
    public function configurationAction(Request $request): Response
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
     */
    public function deleteConfigurationAction(Configuration $configuration): Response
    {
        $this->getDoctrine()->getManager()->remove($configuration);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('notice', 'Konfiguration '.$configuration->getName().' entfernt');
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
            $this->getDoctrine()->getManager()->persist($configuration);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', 'Konfiguration '.$configuration->getName().' erstellt');
        }
        return $this->redirectToRoute('configuration_list');
    }

}
