<?php
 
namespace SE\ReportBundle\Refresher;
 
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use SE\InputBundle\Entity\UserInput;
 
class RefresherListener {
 
    public function onFlush(OnFlushEventArgs  $args) {
        $this->setRefresher($args);
    }

    public function postRemove(LifecycleEventArgs $args) {
  //      $this->setRefresher($args);
    }

    public function setRefresher($args) {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof UserInput) {
                $period = array('month' => $entity->getDateInput()->format('m'), 'year' => $entity->getDateInput()->format('Y'));
                $Attendance = $em->getRepository('SE\ReportBundle\Entity\AttendanceData')->findOneBy($period);
                if($Attendance && $Attendance->getRefresher() == 0){
                    $Attendance->setRefresher(1);
                    $em->persist($Attendance);
                    $classMetadata = $em->getClassMetadata('SE\ReportBundle\Entity\AttendanceData');
                    $uow->computeChangeSet($classMetadata, $Attendance);
                }
            }
        }
    }
}
