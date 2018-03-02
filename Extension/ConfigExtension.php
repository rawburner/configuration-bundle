<?php
namespace ConfigurationBundle\Extension;
use ConfigurationBundle\Entity\ConfigurationRepository;

/**
 * Class ConfigExtension
 */
class ConfigExtension extends \Twig_Extension
{

    /** @var  \Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * ConfigExtension constructor.
     * @param $em
     */
    public function __construct(
        $em
    )
    {
        $this->em = $em;
    }

    /**
     * @author Alexander Keil
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getConfigVar', [$this, 'getConfigVar']),
            new \Twig_SimpleFunction('getConfigGroups', [$this, 'getConfigurationGroups']),
            new \Twig_SimpleFunction('getConfigForGroup', [$this, 'getConfigurationByGroup']),
            new \Twig_SimpleFunction('getConfigVarAsArray', [$this, 'getConfigVarAsArray']),
        ];
    }

    /**
     *
     * @param $groupName
     * @param bool $withPrivate
     * @return array
     */
    public function getConfigurationByGroup($groupName,$withPrivate = false){
        /** @var ConfigurationRepository $repo */
        $repo =  $this->em->getRepository('ConfigurationBundle:Configuration');
        $findBy = [
            'group' => $groupName,
            'public' => 1
        ];
        if($withPrivate){
            unset($findBy['public']);
        }
        return $repo->findBy($findBy, ['name' => 'asc']);
    }

    /**
     * @param bool $withPrivate
     * @return array
     */
    public function getConfigurationGroups($withPrivate = false){
        /** @var ConfigurationRepository $repo */
        $repo =  $this->em->getRepository('ConfigurationBundle:Configuration');
        return $repo->getOnlyGroups($withPrivate);
    }


    /**
     * @param $name
     * @return array
     */
    public function getConfigVarAsArray($name)
    {
        return $this->em->getRepository('ConfigurationBundle:Configuration')->getConfigVarAsArray($name);
    }

    /**
     * @author Alexander Keil
     * @param $name
     * @return string
     */
    public function getConfigVar($name, $fallback=null)
    {
       return $this->em->getRepository('ConfigurationBundle:Configuration')->getConfigVar($name, $fallback);
    }
}