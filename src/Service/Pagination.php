<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination {

    private $entityClass;
    private $limit=10;
    private $currentPage=1;
    private $manager;

    private $twig;
    private $route;

    private $templatePath;

    public function __construct(EntityManagerInterface $manager, Environment $twig,RequestStack $request,$templatePath)
    {
        //get route of the current page
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager= $manager;
        $this->twig=$twig;
        $this->templatePath= $templatePath;
    }

    public function display(){
        //Appel le moteur twig et préciser le template

        $this->twig->display($this->templatePath, [
            //options pour affichage des données (variables:pages/page/route)
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]);
    }

    //1- Utiliser la pagination à partir de n'import controller = on précise l'entité concernée


    public function setEntityClass($entityClass){
        //entityClass va être envoyée
        $this->entityClass= $entityClass;
        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }
    //2- une limite.

    public function getLimit(){
        return $this->limit;
    }

    public function setLimit($limit){
        $this->limit=$limit;
        return $this;
    }

    //3- La page actuelle

    public function getPage(){
        return $this->currentPage;
    }

    public function setPage($page){
        $this->currentPage=$page;
        return $this;

    }

    //4-Le nombre le page au total

    public function getData(){

        //renseigner l'entité
        if (empty($this->entityClass)) {
            throw new \Exception("setEntityClass n'a pas été renseigné dans le controller correspondant.");
        }

        //calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        //repository trouve les elements
        $repo = $this->manager->getRepository($this->entityClass);

        //construction de la requete
        $data = $repo->findBy([],[],$this->limit,$offset);

        return $data;
    }

    public function getPages(){
                $repo=$this->manager->getRepository($this->entityClass);
                //Le total des annonces
                $total= count($repo->findAll());
                //Le nombre des pages. Ceil pour ne pas avoir des virgules 
                $pages= ceil($total/$this->limit);

                return $pages;
    }

    public function getRoute(){
       return $this->route;
    }

    public function setRoute($route){
        $this->route = $route;
        return $this;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath){
        $this->templatePath = $templatePath;
        return $this;
    }
}