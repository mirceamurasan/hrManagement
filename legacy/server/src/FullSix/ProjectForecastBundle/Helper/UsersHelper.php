<?php

namespace FullSix\ProjectForecastBundle\Helper;

class UsersHelper {

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function mergeTwoUsers($destinationId, $sourceId)
    {
        $mergedUser = $this->em->getRepository('FullSixProjectForecastBundle:Users')->find($sourceId);
        $masterUser = $this->em->getRepository('FullSixProjectForecastBundle:Users')->find($destinationId);

        if (!$masterUser->getWorker() || !$mergedUser->getWorker()) {

            if ($masterUser->getLogin() == null) {
                $masterUser->setLogin($mergedUser->getLogin());
            }

            $this->em->getRepository('FullSixProjectForecastBundle:Users')->mergeUsers($destinationId, $sourceId);

            $this->em->persist($masterUser);

            return true;
        } else {
            return false;
        }
    }
}
