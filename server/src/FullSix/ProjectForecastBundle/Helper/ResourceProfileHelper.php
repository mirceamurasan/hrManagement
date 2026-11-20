<?php

namespace FullSix\ProjectForecastBundle\Helper;

use FullSix\ProjectForecastBundle\Entity\ResourceProfile;
use FullSix\ProjectForecastBundle\Entity\ResourceProfileFlat;

class ResourceProfileHelper {

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function addConnectionBetweenCustomerAndWorker($customerId, $workerId)
    {

        $resourceProfileDuplicate = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfile')->findOneBy(array(
            'customerid' => $customerId,
            'workerid' => $workerId
            ));

        if (!$resourceProfileDuplicate) {
            $resourceProfileEntity = new ResourceProfile();

            $customer = $this->em->getRepository('FullSixProjectForecastBundle:Customers')->find($customerId);
            $worker = $this->em->getRepository('FullSixProjectForecastBundle:Workers')->find($workerId);
            $roles = $worker->getUser()->getRoles();

            $projectsAlreadyAssociatedToWorker = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findBy(array('workerid' => $worker));

            $projectIds = array();

            foreach ($projectsAlreadyAssociatedToWorker as $projectAlreadyAssociatedToWorker) {
                $projectIds[] = $projectAlreadyAssociatedToWorker->getProjectid()->getId();
            }

            $projects = $this->em->getRepository('FullSixProjectForecastBundle:Projects')->getProjectsThatAreNotAssignedToCustomer($customer, $projectIds);

            foreach ($projects as $project) {

                $resourceProfileFlatEntity = new ResourceProfileFlat();

                $resourceProfileFlatEntity->setProjectid($project);
                $resourceProfileFlatEntity->setWorkerid($worker);
                $resourceProfileFlatEntity->setRoles($roles);

                $this->em->persist($resourceProfileFlatEntity);

            }
            $resourceProfileEntity->setCustomerid($customer);
            $resourceProfileEntity->setWorkerid($worker);
            $resourceProfileEntity->setRoles($roles);

            $this->em->persist($resourceProfileEntity);
            $this->em->flush();
        }
    }

    public function updateRolesOnResourceProfilesByWorkers($worker)
    {
        $roles = $worker->getUser()->getRoles();
//        $resourceProfiles = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfile')->updateWorkerRoles($worker);
//        $resourceProfilesFlat = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->updateWorkerRoles($worker);
        $resourceProfiles = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfile')->findBy(array('workerid' => $worker));
        $resourceProfilesFlat = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findBy(array('workerid' => $worker));

        foreach ($resourceProfiles as $resourceProfile) {
            $resourceProfile->setRoles($roles);
            $this->em->persist($resourceProfile);
        }

        foreach ($resourceProfilesFlat as $resourceProfileFlat) {
            $resourceProfileFlat->setRoles($roles);
            $this->em->persist($resourceProfileFlat);
        }
    }

    public function addNewResourceProfileFlatEntity($project, $worker)
    {
        $resourceProfileFlat = new ResourceProfileFlat();

        $resourceProfileFlatDuplicate = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findOneBy(array(
            'workerid' => $worker,
            'projectid' => $project,
        ));

        if (!$resourceProfileFlatDuplicate) {
            $resourceProfileFlat->setProjectid($project);
            $resourceProfileFlat->setWorkerid($worker);
            $resourceProfileFlat->setRoles($worker->getUser()->getRoles());

            $this->em->persist($resourceProfileFlat);
        }
    }

    public function changeProjectCustomer($oldCustomer, $newCustomer, $project)
    {
        if ($oldCustomer != $newCustomer) {
            $oldCustomerResourceProfiles = $oldCustomer->getResourceProfiles();

            /* delete all old client's' workers from current project  */
            foreach ($oldCustomerResourceProfiles as $resourceProfile) {

                $resourceProfileFlat = $this->em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findOneBy(array(
                    'projectid' => $project,
                    'workerid' => $resourceProfile->getWorkerid()
                ));

                if ($resourceProfileFlat) {
                    $this->em->remove($resourceProfileFlat);
                }
                $this->em->flush();
            }

            $newCustomerResourceProfiles = $newCustomer->getResourceProfiles();

            /* add new client's' workers on current project  */
            foreach ($newCustomerResourceProfiles as $resourceProfile) {
                $this->addNewResourceProfileFlatEntity($project, $resourceProfile->getWorkerid());
            }
        }
    }


}
