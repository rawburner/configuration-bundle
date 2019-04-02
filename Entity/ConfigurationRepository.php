<?php

namespace ConfigurationBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ConfigurationRepository extends EntityRepository
{

    /**
     * Cache for runtime values
     * @var array
     */
    protected $cacheValues = [];
    /**
     * @var null|array
     */
    protected $cacheConfigurations = null;

    /**
     * Get config var if exists
     * @param $name
     * @param string $fallback
     * @return string
     */
    public function getConfigVar(string $name, $fallback=null)
    {
        if(array_key_exists($name,$this->cacheValues)){
            return $this->cacheValues[$name];
        }
        /** @var Configuration $config */
        $config = $this->getCachedConfiguration($name);

        switch(true){
            case $config instanceof Configuration && $config->getType() == 'dropdown' && $config->getValue() == 'false':
                $this->cacheValues[$name] = false;
                return false;
            case $config instanceof Configuration && $config->getType() == 'dropdown' && $config->getValue() == 'true':
                $this->cacheValues[$name] = true;
                return true;
            case (empty($config) || !$config->getValue()) && $fallback !== null:
                $this->cacheValues[$name] = $fallback;
                break;
            case empty($config):
            case !$config->getValue():
                $this->cacheValues[$name] = 'configuration '.$name.' not found';
                break;
            default:
                $this->cacheValues[$name] = $config->getValue();
        }
        return $this->cacheValues[$name];

    }

    /**
     * @param $name
     * @return Configuration|null
     */
    protected function getCachedConfiguration($name){
        $name = trim($name);
        if($this->cacheConfigurations === null){
            $this->cacheConfigurations = [];
            /** @var Configuration $configuration */
            foreach ($this->findAll() as $configuration){
                $this->cacheConfigurations[$configuration->getName()] = $configuration;
            }
        }
        if(array_key_exists($name, $this->cacheConfigurations)){
            return $this->cacheConfigurations[$name];
        }
        return null;
    }

    /**
     * Arrays können in der Textarea auch in dieser Form gespeichert werden:
     * Name1 => Wert1
     * Name2 => Wert2
     * @param $name
     * @return array
     */
    public function getConfigVarAsArray(string $name)
    {
        if(array_key_exists($name,$this->cacheValues)){
            return $this->cacheValues[$name];
        }
        $value = $this->getConfigVar($name);
        if(strpos($value, 'not found') !== false){
            return [];
        }
        $returnValues = [];
        $values = explode("\n", $value);
        foreach ($values as $index => $value){
            if(strpos($value, '=>') !== false){
                $tmpValue = explode('=>', $value);
                $returnValues[trim($tmpValue[0])] = trim($tmpValue[1]);
            }else{
                $returnValues[$index] = trim($value);
            }
        }
        $this->cacheValues[$name] = $returnValues;
        return $returnValues;
    }

    public function addVarToConfigArray(string $name, string $value, string $group = 'Systemeinstellungen'){
        /** @var Configuration $config */
        $config = $this->findOneBy([
            'name' => trim($name)
        ]) ;
        if(empty($config)){
            $config = new Configuration();
            $config->setValue(trim($value))
                ->setName($name)
                ->setGroup($group)
                ->setType('dropdown');
        }else{
            $newValue = $config->getValue();
            $newValue .= "\n".trim($value);
            $arValue = explode("\n", $newValue);
            $arValue = array_unique($arValue);
            $config->setValue(implode("\n", $arValue));
        }
        $this->_em->persist($config);
        $this->_em->flush();
        $this->cacheValues = [];
    }


    public function isConfigVarExists(string $name): bool
    {
        $config = $this->getCachedConfiguration($name);

        if(!$config){
            return false;
        }
        return true;
    }

    public function getOnlyGroups($withPrivate = false){
        $query = $this->createQueryBuilder('c');
        $query->select('c.group');
        if($withPrivate){
            $query->where('c.public <= 1' );
        }else{
            $query->where('c.public = 1' );
        }
        $query->addGroupBy('c.group');
        return $query->getQuery()->getResult();
    }
}
