<?php
namespace ConfigurationBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Configuration
 *
 * @ORM\Table(name="configuration")
 * @ORM\Entity(repositoryClass="ConfigurationBundle\Entity\ConfigurationRepository")
 */
class Configuration
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="name", type="string", nullable=true, length=255)
     */
    private $name;

    /**
     * @var integer
     * @Column(name="public", type="integer", nullable=false, options={"default" : 0})
     */
    private $public = 0;

    /**
     * @var string
     *
     * @Column(name="confgroup", type="string", nullable=true, length=255)
     */
    private $group;

    /**
     * @var string
     *
     * @Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var string
     *
     * @Column(name="type", type="text", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @Column(name="options", type="text", nullable=true)
     */
    private $options;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Configuration
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Configuration
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * @param int $public
     * @return Configuration
     */
    public function setPublic($public)
    {
        $this->public = $public;
        return $this;
    }


    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     * @return Configuration
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Configuration
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Configuration
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     * @return Configuration
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getOptionsArray(){
        if(empty($this->options)){
            return [];
        }
        return explode("\n", $this->options);
    }

}