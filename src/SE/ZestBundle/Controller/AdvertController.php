<?php

namespace SE\ZestBundle\Controller;

use SE\ZestBundle\Entity\Advert;
use SE\ZestBundle\Entity\Image;
use SE\ZestBundle\Entity\Application;
use SE\ZestBundle\Entity\AdvertSkill;
use SE\ZestBundle\Form\AdvertType;
use SE\ZestBundle\Form\AdvertEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
	public function indexAction($page)
	{
    if ($page < 1) {
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }

    $nbPerPage = 5;

    // On récupère toutes les annonces
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('SEZestBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts)/$nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages && $page !== 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

      return $this->render('SEZestBundle:Advert:index.html.twig', array(
       'listAdverts' => $listAdverts,
       'nbPages'     => $nbPages,
       'page' => $page
      ));
    }

  public function viewAction($id)
  {

    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em
      ->getRepository('SEZestBundle:Advert')
      ->find($id)
    ;

    // $advert est donc une instance de SE\ZestBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On récupère la liste des candidatures de cette annonce
    $listApplications = $em
      ->getRepository('SEZestBundle:Application')
      ->findBy(array('advert' => $advert))
    ;

    // On récupère maintenant la liste des AdvertSkill
    $listAdvertSkills = $em
      ->getRepository('SEZestBundle:AdvertSkill')
      ->findBy(array('advert' => $advert))
    ;

    // Le render ne change pas, on passait avant un tableau, maintenant un objet
    return $this->render('SEZestBundle:Advert:view.html.twig', array(
      'advert' => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
    ));
  }

  public function addAction(Request $request)
    {
     $advert = new Advert();
     $form = $this->createForm(new AdvertType, $advert);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      return $this->redirect($this->generateUrl('se_zest_view', array('id' => $advert->getId())));
    }

    // À ce stade, le formulaire n'est pas valide car :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
    return $this->render('SEZestBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function editAction(Request $request, $id)
  {
 
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('SEZestBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $form = $this->createForm(new AdvertEditType(), $advert);

   if ($form->handleRequest($request)->isValid()) {
     $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiee.');

      return $this->redirect($this->generateUrl('se_zest_view', array('id' => $advert->getId())));
    }

    return $this->render('SEZestBundle:Advert:edit.html.twig', array(
      'form' => $form->createView(),
      'advert' => $advert
    ));
  }

  public function deleteAction($id, Request $request)
  { 
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('SEZestBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

      return $this->redirect($this->generateUrl('se_zest_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('SEZestBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView()
    ));
  }

  public function menuAction($limit = 3)
  {
    $em = $this->getDoctrine()->getManager();

    $listAdverts = $em
      ->getRepository('SEZestBundle:Advert')
      ->findBy(
        array(),
        array('date' => 'desc'),
        $limit,
        0
      );

    if (null === $listAdverts) {
      throw new NotFoundHttpException("Aucune annonce pour le moment");
    }

    return $this->render('SEZestBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
}