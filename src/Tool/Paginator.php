<?php



namespace App\Tool;



class Paginator

{

    // Numéro de la page active

    protected $current;



    // Nombre de pages au total

    protected $nbPage;



    // Nombre d'élément par page

    protected $nbByPage;



    // Nombre total d'élément

    protected $nbTotal;



    public function __construct($Page = 1 , $NbByPage = 12, $NbTotal = 1) {

        $this->current = $Page;



        $this->nbByPage = (int)$NbByPage;



        $this->nbTotal = (int)$NbTotal;



        if($this->nbByPage > 0 && $this->nbByPage != null){

            $this->nbPage = ((int) ceil($this->nbTotal / $this->nbByPage));

        } else {

            $this->nbPage = 1;

        }

    }



    public function setPage($Page){

        $this->current = $Page;

    }



    public function getCurrent(){

        return $this->current;

    }



    public function setNbTotal($NbTotal){

        $this->nbTotal = $NbTotal;



        $this->nbPage = (

            (int) ceil(

                $this->nbTotal /

                $this->nbByPage));

    }



    public function hasPrevious(){

        if($this->current > 1) return true;



        return false;

    }



    public function hasNext(){

        if($this->current < $this->nbPage) return true;



        return false;

    }



    public function previous() {

        return (int) $this->current - 1;

    }



    public function next() {

        return (int) $this->current + 1;

    }



    public function getNbPage() {

        return $this->nbPage;

    }



    public function getNbByPage() {

        return $this->nbByPage;

    }



    public function getNbTotal() {

        return $this->nbTotal;

    }



    public function setNbByPage($nbByPage) {

        $this->nbByPage = $nbByPage;

    }



    public function getStart() {

        return (int) (($this->current -1)*$this->nbByPage);

    }

}