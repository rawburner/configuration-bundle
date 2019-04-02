<?php
namespace ConfigurationBundle\Extension;
use ConfigurationBundle\Entity\Configuration;
use ConfigurationBundle\Entity\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConfigExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('getConfigVar', [$this, 'getConfigVar']),
            new \Twig_SimpleFunction('getConfigGroups', [$this, 'getConfigurationGroups']),
            new \Twig_SimpleFunction('getConfigForGroup', [$this, 'getConfigurationByGroup']),
            new \Twig_SimpleFunction('getConfigVarAsArray', [$this, 'getConfigVarAsArray']),
        ];
    }

    public function getConfigurationByGroup($groupName,$withPrivate = false){
        /** @var ConfigurationRepository $repo */
        $repo =  $this->em->getRepository(Configuration::class);
        $findBy = [
            'group' => $groupName,
            'public' => 1
        ];
        if($withPrivate){
            unset($findBy['public']);
        }
        return $repo->findBy($findBy, ['name' => 'asc']);
    }

    public function getConfigurationGroups($withPrivate = false){
        /** @var ConfigurationRepository $repo */
        $repo =  $this->em->getRepository(Configuration::class);
        return $repo->getOnlyGroups($withPrivate);
    }


    public function getConfigVarAsArray(string $name)
    {
        return $this->em->getRepository(Configuration::class)->getConfigVarAsArray($name);
    }

    public function getConfigVar(string $name, $fallback=null)
    {
       return $this->em->getRepository(Configuration::class)->getConfigVar($name, $fallback);
    }
}
