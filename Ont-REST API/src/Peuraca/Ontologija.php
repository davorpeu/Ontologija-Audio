<?php

namespace Peuraca;

/**
 * @Entity @Table(name="ontologija")
 **/


class Ontologija
{
    /** @id @Column(type="integer") @GeneratedValue **/
    protected $sifra;


/**
    * @Column(type="string")
    */
    private $naslov;

    /**
    * @Column(type="string")
    */
    private $tip;

    /**
    * @Column(type="string")
    */
    private $duzina;

   
    /**
    * @Column(type="string")
    */
    private $autor;

  	public function getSifra(){
      return $this->sifra;
    }
  
    public function setSifra($sifra){
      $this->sifra = $sifra;
    }

    public function getNaslov(){
      return $this->naslov;
    }
  
    public function setNaslov($naslov){
      $this->naslov = $naslov;
    }
  
    public function getTip(){
      return $this->tip;
    }
  
    public function setTip($tip){
      $this->tip = $tip;
    }
  
    public function getDuzina(){
      return $this->duzina;
    }
  
    public function setDuzina($duzina){
      $this->duzina = $duzina;
    }
  
    public function getAutor(){
      return $this->autor;
    }
  
    public function setAutor($autor){
      $this->autor = $autor;
    }

  public function setPodaci($podaci)
	{
		foreach($podaci as $kljuc => $vrijednost){
			$this->{$kljuc} = $vrijednost;
		}
	}

}



?>
