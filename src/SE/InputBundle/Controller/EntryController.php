<?php

namespace SE\InputBundle\Controller;

use SE\InputBundle\Entity\Employee;
use SE\InputBundle\Entity\UserInput;
use SE\InputBundle\Form\UserInputType;
use SE\InputBundle\Entity\InputReview;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class EntryController extends Controller
{
	public function inputAction(Request $request)
	{
    $listEmployees = $this->getDoctrine()
      ->getManager()
      ->getRepository('SEInputBundle:Employee')
      ->getAlphaEmployees()
    ;

    $userInput = new UserInput();
    $form = $this->createForm(new UserInputType(), $userInput);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($userInput);
      $em->flush();

    $request->getSession()->getFlashBag()->add('notice', 'new working hours entry saved');

    return $this->redirect($this->generateUrl('se_input_review'));
    }

    return $this->render('SEInputBundle:Entry:input_form.html.twig', array(
    'form' => $form->createView(),
    'listEmployees' => $listEmployees
    ));
	}

	public function menuAction()
  	{
    	return $this->render('SEInputBundle:Entry:menu.html.twig');
  	}

  public function welcomeAction()
    {
      return $this->render('SEInputBundle:Entry:welcome.html.twig');
    }

  public function reviewAction()
    {
      $em = $this->getDoctrine()->getManager();

      //select errors
      $importErrors = $em->getRepository('SEInputBundle:InputReview')
       ->getLastMonthImportErrors();


      $inputErrors = $em->getRepository('SEInputBundle:InputReview')
       ->getLastMonthMissingInputErrors();

       //check if input has been filled in the meantime, remove the error if so
      foreach ($inputErrors as $errorToCheck) {
        if($em->getRepository('SEInputBundle:UserInput')->findOneBy(array('dateInput' => $errorToCheck->getDate(), 'team' => $errorToCheck->getTeam(), 'shift' =>  $errorToCheck->getShift()))){
          $em->remove($errorToCheck);
          $em->flush();
        }
      }

      $toErrors = $em->getRepository('SEInputBundle:InputReview')
       ->getLastMonthToErrors();

      $hourErrors = $em->getRepository('SEInputBundle:InputReview')
       ->getLastMonthHoursErrors();

      $userInputs = $em->getRepository('SEInputBundle:UserInput')
       ->getLastMonth();

      return $this->render('SEInputBundle:Entry:review.html.twig', array(
        'importErrors' => $importErrors,
        'inputErrors' => $inputErrors,
        'toErrors' => $toErrors,
        'hourErrors' => $hourErrors,
        'userInputs' => $userInputs
      ));
    }

  public function ignoreAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idInput = $request->get('idInput');
    
    $ignoreInput = $em->getRepository('SEInputBundle:InputReview')->findOneBy(array('id' => $idInput));

    if ($ignoreInput){
    
      $ignoreInput->setStatus(1);
      $em->persist($ignoreInput);
      $em->flush();

      $response = array("code" => 100, "success" => true);
    }else{
      $response = array("code" => 400, "success" => false);
    }
    return new Response(json_encode($response)); 
  }

  public function deleteAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idInput = $request->get('idInput');
    
    $deleteInput = $em->getRepository('SEInputBundle:UserInput')->findOneBy(array('id' => $idInput));
    if($deleteInput){
      $deleteEntries = $em->getRepository('SEInputBundle:InputEntry')->findBy(array('user_input' => $idInput));
      if($deleteEntries){
        foreach ($deleteEntries as $deleteEntry) {
          $deleteActivityHours = $em->getRepository('SEInputBundle:ActivityHours')->findBy(array('input' => $deleteEntry));
          if ($deleteActivityHours) {
            foreach ($deleteActivityHours as $deleteActivityHour) {
              // un-record to lines too
              foreach ($em->getRepository('SEInputBundle:SAPRF')->getRecordedTo($deleteInput->getDateInput(), $deleteEntry->getSesa()) as $recordedTo) {
                $recordedTo->setRecorded(0);
              }
              $em->remove($deleteActivityHour);
            }
          }
          $em->remove($deleteEntry);
        }
      }
      $em->remove($deleteInput);
      $em->flush();

      $response = array("code" => 100, "success" => true);

    }else{
      $response = array("code" => 400, "success" => false);
    }

    return new Response(json_encode($response)); 
  }

  public function timeReviewAction(){
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $year = $request->get('year');
    $month = $request->get('month');
  
    //select errors
    $importErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthImportErrors($month, $year);
    $inputErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthMissingInputErrors($month, $year);
    $toErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthToErrors($month, $year);
    $hourErrors = $em->getRepository('SEInputBundle:InputReview')->getMonthHoursErrors($month, $year);
    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getMonthInputs($month, $year);

    $hTemplate = array();
    $eTemplate = array();
    
    foreach ($userInputs as $u) {
      $hView = $this->render('SEInputBundle:Utilities:historyView.html.twig', array('input' => $u))->getContent();
      $hTemplate[] = array($u->getDateInput()->format('d-m-Y'),
                           $u->getTeam()->getName(),
                           $u->getShift()->getId(),
                           $u->getTotalHoursInput(),
                           $u->getTotalHeadcount(),
                           $u->getTotalToInput(),
                           $u->getTotalProdInput(),
                           $hView);
    }

    foreach ($inputErrors as $i) {
      $eView = $this->render('SEInputBundle:Utilities:errorsView.html.twig', array('input' => $i))->getContent();
      $eTemplate[] = array($i->getDate()->format('d-m-Y'),$i->getTeam()->getName(),$i->getShift()->getId(),$eView);
    }

    $response = array("code" => 100, "success" => true, "m" => sizeof($importErrors), "i" => sizeof($inputErrors), "t" => sizeof($toErrors), "h" => sizeof($hourErrors), "hTemplate" => $hTemplate, "eTemplate" => $eTemplate);
    return new Response(json_encode($response)); 
  }

  public function populateAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $idEmployee = $request->get('idEmployee');
    
    $addEmployee = $em->getRepository('SEInputBundle:Employee')->findOneBy(array('id' => $idEmployee));
    if($addEmployee){
      $sesa = $addEmployee->getSesa();
      $activity = $addEmployee->getDefaultActivity()->getId();
      $response = array("code" => 100, "success" => true, "sesa" => $sesa, "activity" => $activity);

    }else{
      $response = array("code" => 400, "success" => false);
    }

    return new Response(json_encode($response)); 
  }

  public function reviewDetailsAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $userInput = $em
      ->getRepository('SEInputBundle:UserInput')
      ->find($id)
    ;

    if (null === $userInput) {
      throw new NotFoundHttpException("The manhours input ".$id." was not found. Sorry lah.");
    }

    return $this->render('SEInputBundle:Entry:review_details.html.twig', array(
      'input' => $userInput
    ));
  }

  public function activitiesAction()
  { 
    $em = $this->getDoctrine()->getManager();
    $request = $this->get('request');        
    $id = $request->get('id');
    
    $input = $em
      ->getRepository('SEInputBundle:UserInput')
      ->find($id)
    ;

    $jsonActivities = array(
      'cat' => array(),
      'data' => array()
      );

    if($input){
      foreach ($input->getInputEntries() as $entry) {
        foreach ($entry->getActivityHours() as $a) {
         $key = array_search($a->getActivity()->getName(), $jsonActivities['cat']);
         if($key === false){
            $jsonActivities['cat'][] = $a->getActivity()->getName();
            $key = array_search($a->getActivity()->getName(), $jsonActivities['cat']);
          }
          if(array_key_exists($key, $jsonActivities['data'])){
            $jsonActivities['data'][$key] += $a->getRegularHours() + $a->getOtHours();
          }else{
            $jsonActivities['data'][$key] = $a->getRegularHours() + $a->getOtHours();
          }       
        }
      }
      $response = array("code" => 100, "success" => true, "jsonActivities" => $jsonActivities);

    }else{
      $response = array("code" => 400, "success" => false, "jsonActivities" => $jsonActivities);
    }

    return new Response(json_encode($response)); 
  }

  public function missingInputAction()
  {
    $em = $this->getDoctrine()->getManager();
    $teams = $em->getRepository('SEInputBundle:Team')->getReportingTeams();
    $userInputs = $em->getRepository('SEInputBundle:UserInput')->getLastMonth();
    $inputIssue = $em->getRepository('SEInputBundle:TypeIssue')->find(2);

    $today = new \DateTime();
    $today->setTime(00, 00, 00);
    $lastMonth = new \DateTime();
    $lastMonth->setTime(00, 00, 00)->modify( '-'.(date('j')-1).' day' );
    $daydiff = $today->diff($lastMonth)->days;
    $found = false;

    for ($i=0; $i < $daydiff; $i++) { //pour chaque jour
        $dateCheck = new \DateTime();
        $dateCheck->setTime(00, 00, 00)->modify( '-'.($i+1).' day' );
      $d = $dateCheck->format("Y-m-d");
      foreach ($teams as $team) { //pour chaque team
        $t = $team->getId();
        for ($s=1; $s <= $team->getShiftnb(); $s++) { //pour chaque shift 
          $shift = $em->getRepository('SEInputBundle:Shift')->findOneBy(array('id' => $s));
          foreach ($userInputs as $u){  // pour chaque userinput 
            if(($u->getDateInput()->format("Y-m-d") == $d) and ($u->getTeam() == $team) and ($u->getShift() == $shift)){$found = true;}
          }
          if(!$found){
            if(!($em->getRepository('SEInputBundle:InputReview')->findOneBy(array('date' => $dateCheck, 'type' => $inputIssue, 'team' => $team, 'shift' =>  $shift)))){
                $m = new InputReview();
                $m->setDate($dateCheck);
                $m->setType($inputIssue);
                $m->setStatus(0);
                $m->setTeam($team);
                $m->setShift($shift);
                $em->persist($m);

                $em->flush();
              }
          }else{
            $found = false;
          }
        }
      }
    }
    $response = array("code" => 100, "success" => true);

    return new Response(json_encode($response)); 
  }
}