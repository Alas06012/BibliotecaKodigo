<?php
class Libro {
    private $id;
    private $titulo;
    private $autor;
    private $categoria;
    private $disponible;

    public function __construct($idParam, $tituloParam, $autorParam, $categoriaParam, $disponibleParam = true) {
        $this->id = $idParam;
        $this->titulo = $tituloParam;
        $this->autor = $autorParam;
        $this->categoria = $categoriaParam;
        $this->disponible = $disponibleParam;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($idParam) {
        $this->id = $idParam;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function setTitulo($tituloParam) {
        $this->titulo = $tituloParam;
    }

    public function getAutor() {
        return $this->autor;
    }

    public function setAutor($autorParam) {
        $this->autor = $autorParam;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function setCategoria($categoriaParam) {
        $this->categoria = $categoriaParam;
    }

    public function getDisponible() {
        return $this->disponible;
    }

    public function setDisponible($disponibleParam) {
        $this->disponible = $disponibleParam;
    }
}
?>