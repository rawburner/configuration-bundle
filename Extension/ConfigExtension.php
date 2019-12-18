<?php
namespace ConfigurationBundle\Extension;
use ConfigurationBundle\Entity\Configuration;
use ConfigurationBundle\Entity\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigExtension extends AbstractExtension
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
            new TwigFunction('getConfigVar', [$this, 'getConfigVar']),
            new TwigFunction('getConfigGroups', [$this, 'getConfigurationGroups']),
            new TwigFunction('getConfigForGroup', [$this, 'getConfigurationByGroup']),
            new TwigFunction('getConfigVarAsArray', [$this, 'getConfigVarAsArray']),
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
