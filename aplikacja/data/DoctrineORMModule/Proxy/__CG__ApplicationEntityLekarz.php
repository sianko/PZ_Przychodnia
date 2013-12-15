<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Lekarz extends \Application\Entity\Lekarz implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'lid', 'tytulNaukowy', 'grafik', 'minutNaPacjenta', 'os', 'spec');
        }

        return array('__isInitialized__', 'lid', 'tytulNaukowy', 'grafik', 'minutNaPacjenta', 'os', 'spec');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Lekarz $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getLid()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getLid();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLid', array());

        return parent::getLid();
    }

    /**
     * {@inheritDoc}
     */
    public function setLid($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLid', array($id));

        return parent::setLid($id);
    }

    /**
     * {@inheritDoc}
     */
    public function setTytulNaukowy($tytulNaukowy)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTytulNaukowy', array($tytulNaukowy));

        return parent::setTytulNaukowy($tytulNaukowy);
    }

    /**
     * {@inheritDoc}
     */
    public function getTytulNaukowy()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTytulNaukowy', array());

        return parent::getTytulNaukowy();
    }

    /**
     * {@inheritDoc}
     */
    public function setGrafik($grafik)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGrafik', array($grafik));

        return parent::setGrafik($grafik);
    }

    /**
     * {@inheritDoc}
     */
    public function getGrafik()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGrafik', array());

        return parent::getGrafik();
    }

    /**
     * {@inheritDoc}
     */
    public function getGrafikArray()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGrafikArray', array());

        return parent::getGrafikArray();
    }

    /**
     * {@inheritDoc}
     */
    public function setGrafikArray($array)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGrafikArray', array($array));

        return parent::setGrafikArray($array);
    }

    /**
     * {@inheritDoc}
     */
    public function setMinutNaPacjenta($minutNaPacjenta)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMinutNaPacjenta', array($minutNaPacjenta));

        return parent::setMinutNaPacjenta($minutNaPacjenta);
    }

    /**
     * {@inheritDoc}
     */
    public function getMinutNaPacjenta()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMinutNaPacjenta', array());

        return parent::getMinutNaPacjenta();
    }

    /**
     * {@inheritDoc}
     */
    public function setOs(\Application\Entity\Osoba $os = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOs', array($os));

        return parent::setOs($os);
    }

    /**
     * {@inheritDoc}
     */
    public function getOs()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOs', array());

        return parent::getOs();
    }

    /**
     * {@inheritDoc}
     */
    public function setSpec(\Application\Entity\Specjalnosc $spec = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSpec', array($spec));

        return parent::setSpec($spec);
    }

    /**
     * {@inheritDoc}
     */
    public function getSpec()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSpec', array());

        return parent::getSpec();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', array($id));

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function setImie($imie)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setImie', array($imie));

        return parent::setImie($imie);
    }

    /**
     * {@inheritDoc}
     */
    public function getImie()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getImie', array());

        return parent::getImie();
    }

    /**
     * {@inheritDoc}
     */
    public function setNazwisko($nazwisko)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNazwisko', array($nazwisko));

        return parent::setNazwisko($nazwisko);
    }

    /**
     * {@inheritDoc}
     */
    public function getNazwisko()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNazwisko', array());

        return parent::getNazwisko();
    }

    /**
     * {@inheritDoc}
     */
    public function setPesel($pesel)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPesel', array($pesel));

        return parent::setPesel($pesel);
    }

    /**
     * {@inheritDoc}
     */
    public function getPesel()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPesel', array());

        return parent::getPesel();
    }

    /**
     * {@inheritDoc}
     */
    public function setAdres($adres)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAdres', array($adres));

        return parent::setAdres($adres);
    }

    /**
     * {@inheritDoc}
     */
    public function getAdres()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAdres', array());

        return parent::getAdres();
    }

    /**
     * {@inheritDoc}
     */
    public function setTelefon($telefon)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTelefon', array($telefon));

        return parent::setTelefon($telefon);
    }

    /**
     * {@inheritDoc}
     */
    public function getTelefon()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTelefon', array());

        return parent::getTelefon();
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEmail', array($email));

        return parent::setEmail($email);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmail', array());

        return parent::getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function setDataUr($dataUr)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDataUr', array($dataUr));

        return parent::setDataUr($dataUr);
    }

    /**
     * {@inheritDoc}
     */
    public function getDataUr()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDataUr', array());

        return parent::getDataUr();
    }

    /**
     * {@inheritDoc}
     */
    public function setPlec($plec)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPlec', array($plec));

        return parent::setPlec($plec);
    }

    /**
     * {@inheritDoc}
     */
    public function getPlec()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPlec', array());

        return parent::getPlec();
    }

    /**
     * {@inheritDoc}
     */
    public function setPoziom($poziom)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPoziom', array($poziom));

        return parent::setPoziom($poziom);
    }

    /**
     * {@inheritDoc}
     */
    public function getPoziom()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPoziom', array());

        return parent::getPoziom();
    }

    /**
     * {@inheritDoc}
     */
    public function setHaslo($haslo)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHaslo', array($haslo));

        return parent::setHaslo($haslo);
    }

    /**
     * {@inheritDoc}
     */
    public function getHaslo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHaslo', array());

        return parent::getHaslo();
    }

    /**
     * {@inheritDoc}
     */
    public function setSol($sol)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSol', array($sol));

        return parent::setSol($sol);
    }

    /**
     * {@inheritDoc}
     */
    public function getSol()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSol', array());

        return parent::getSol();
    }

    /**
     * {@inheritDoc}
     */
    public function setAktywny($aktywny)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAktywny', array($aktywny));

        return parent::setAktywny($aktywny);
    }

    /**
     * {@inheritDoc}
     */
    public function getAktywny()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAktywny', array());

        return parent::getAktywny();
    }

}
