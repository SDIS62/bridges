<?php

class Start_Model_Intervention extends SDIS62_Model_Abstract
{
    /**
     * Année de l'intervention (pour l'identification)
     *
     * @var int
     */
	protected $year;
    
    /**
     * Date du décelenchement de l'intervention
     *
     * @var Datetime
     */
	protected $date_declenchement;
    
    /**
     * Description de l'intervention
     *
     * @var string
     */
	protected $description;
    
    /**
     * Code Insee de la commune
     *
     * @var Start_Model_Commune
     */
	private $location_commune;
    
    /**
     * Localisation de l'intervention - Rue
     *
     * @var string
     */
	private $location_rue;
    
    /**
     * Localisation de l'intervention - X
     *
     * @var float
     */
	private $location_x;
    
    /**
     * Localisation de l'intervention - Y
     *
     * @var float
     */
	private $location_y;
    
    /**
     * Le sinistre associé à l'intervention
     *
     * @var Start_Model_Sinistre
     */
	protected $sinistre;

    /**
     * Liste des centres engagés
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
	protected $centres_engages;
    
	/**
     * La main courante de l'intervention
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
	protected $main_courante;
    
    /**
     * Chronologie de l'intervention
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     */
	protected $chronologie;
    
    /*
     * Constructeur
     * @param array $data Optionnel
     */
    public function __construct($data = array())
    {
        $this->date_declenchement = new \DateTime;
        $this->centres_engages = new Doctrine\Common\Collections\ArrayCollection;
        $this->main_courante = new Doctrine\Common\Collections\ArrayCollection;
        $this->chronologie = new Doctrine\Common\Collections\ArrayCollection;
        
        parent::__construct($data);
    }
    
    /**
     * Récupération de l'identifiant année
     *
     * @return int
     */ 
    public function getYear()
    {
		return $this->year;
	}

    /**
     * Définition de l'identifiant année
     *
     * @param int $year
     * @return Start_Model_Intervention Interface fluide
     */
	public function setYear($id)
    {
		$this->year = $year;
        return $this;
	}
    
    /**
     * Récupération de la description de l'intervention
     *
     * @return string
     */ 
	public function getDescription()
    {
		return $this->description;
	}

    /**
     * Définition de la description de l'intervention
     *
     * @param string $description
     * @return Start_Model_Intervention Interface fluide
     */
	public function setDescription($description)
    {
		$this->description = $description;
        return $this;
	}

    /**
     * Récupération de la date de déclenchement
     *
     * @return Datetime
     */ 
	public function getDateDeclenchement()
    {
		return $this->date_declenchement;
	}

    /**
     * Définition de la date de déclenchement de l'intervention
     *
     * @param string $date_declenchement
     * @return Start_Model_Intervention Interface fluide
     */
	public function setDateDeclenchement($date_declenchement)
    {
        $this->date_declenchement = new \DateTime($date_declenchement, new DateTimeZone('Europe/Paris'));
        return $this;
	}
    
    /**
     * Récupération du sinistre
     *
     * @return Start_Model_Sinistre
     */ 
    public function getSinistre()
    {
		return $this->sinistre;
	}

    /**
     * Définition du sinistre
     *
     * @param Start_Model_Sinistre $sinistre
     * @return Start_Model_Intervention Interface fluide
     */
	public function setSinistre(Start_Model_Sinistre $sinistre)
    {
		$this->sinistre = $sinistre;
        return $this;
	}

    /**
     * Récupération des informations de géolocalisation de l'intervention
     *
     * @return array
     */ 
	public function getLocation()
    {
        // On transforme les coordonnées Lambert II Étendu en WGS 84
        require_once('Geo.php');
        $geo = new GeoConversion;
        $wgs84_point = $geo->Lambert_To_WGS84($this->location_x, $this->location_y);
        
		return array(
            'commune' => $this->location_commune->extract(),
            'rue' => $this->location_rue,
            'x' => $this->location_x,
            'y' => $this->location_y,
            'wgs84_longitude' => $wgs84_point->Longitude,
            'wgs84_latitude' => $wgs84_point->Latitude,
        );
	}

    /**
     * Définition de la localisation de l'intervention
     *
     * @param string $nom_commune
     * @param string $rue
     * @param float $x
     * @param float $y
     * @return Start_Model_Intervention Interface fluide
     */
	public function setLocation($code_insee, $rue, $x = null, $y = null)
    {
		// $this->location_nom_commune = $nom_commune;
		$this->location_commune = new Start_Model_Commune(array('code_insee' => $code_insee));
		$this->location_rue = $rue;
		$this->location_x = $x;
		$this->location_y = $y;
        return $this;
	}

    /**
     * Récupération des centres engagés
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */ 
	public function getCentresEngages()
    {
		return $this->centres_engages;
	}

    /**
     * Définition des centres engagés de l'intervention
     *
     * @param array $centres_engages
     * @return Start_Model_Intervention Interface fluide
     */
	public function setCentresEngages(array $centres_engages)
    {
		$this->centres_engages = $centres_engages;
        return $this;
	}

    /**
     * Récupération de la main courante
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */ 
	public function getMainCourante()
    {
		return $this->main_courante;
	}

    /**
     * Définition de la main courante
     *
     * @param array $main_courante
     * @return Start_Model_Intervention Interface fluide
     */
	public function setMainCourante(array $main_courante)
    {
		$this->main_courante = $main_courante;
        return $this;
	}

    /**
     * Récupération de la chronologie
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */ 
	public function getChronologie()
    {
		return $this->chronologie;
	}

    /**
     * Définition de la chronologie
     *
     * @param array $chronologie
     * @return Start_Model_Intervention Interface fluide
     */
	public function setChronologie(array $chronologie)
    {
		$this->chronologie = $chronologie;
        return $this;
	}
    
    /**
     * @inheritdoc
     */
    public function extract()
    {
        $data = parent::extract();
        
        $data['location'] = $this->getLocation();
        
        return $data;
    }
}