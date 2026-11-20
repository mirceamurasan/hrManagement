<?php

namespace FullSix\ProjectForecastBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class JobsRepository extends EntityRepository
{

    public function planDeChargeByJobsIdOnWeeks($jobsid, $date, $nb_week, $intext = "all")
    {
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('CustomersName', 'CustomersName');
        $rsm->addScalarResult('ProjectsId', 'ProjectsId');
        $rsm->addScalarResult('ProjectsName', 'ProjectsName');
        $rsm->addScalarResult('ProjectsSuccess', 'ProjectsSuccess');
        $rsm->addScalarResult('CotechOperationalUserName', 'CotechOperationalUserName');
        $rsm->addScalarResult('SuperviseurUserName', 'SuperviseurUserName');

        $sql = "SELECT  Customers.CustomersName,
                        Projects.Id AS ProjectsId,
                        Projects.ProjectsName,
                        CotechOperationalUsers.username AS CotechOperationalUserName,
                        SuperviseurUsers.username AS SuperviseurUserName,
                        Projects.ProjectsSuccess";
        
        for ($i=1;$i<=$nb_week;$i++) {
            $sql .= ",
                        SUM(COALESCE(
                            (
                            SELECT      SUM(
                                            CASE WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DaysoffsGlobal.id IS NULL) THEN
                                                1
                                            ELSE
                                                0
                                            END
                                        )
                            FROM        DateAll
                            LEFT JOIN   DaysoffsGlobal ON DateAll.date = DaysoffsGlobal.DaysoffsGlobalDate
                            WHERE       DateAll.Date >=  DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".($i-1)." WEEK)
                            AND         DateAll.Date < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$i." WEEK)
                            AND         DateAll.Date BETWEEN TmpTasks.TasksBeginAt AND TmpTasks.TasksEndAt
                            )*(
                            (TasksLoads.TasksLoadsVolume*Projects.ProjectsSuccess/100) / OpenDays
                        ), 0)) as week_".$i;
            $rsm->addScalarResult('week_'.$i, 'week_'.$i);
        }
        $sql .= "
                FROM        TasksLoads
                JOIN        (
                            SELECT *,
                                (
                                    SELECT      SUM(
                                                    CASE WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DaysoffsGlobal.id IS NULL) THEN
                                                        1
                                                    ELSE
                                                        0
                                                    END
                                                )
                                    FROM        DateAll
                                    LEFT JOIN   DaysoffsGlobal ON DateAll.date = DaysoffsGlobal.DaysoffsGlobalDate
                                    WHERE       DateAll.Date BETWEEN Tasks.TasksBeginAt AND Tasks.TasksEndAt
                                ) as OpenDays
                            FROM Tasks
                            ) TmpTasks ON TasksLoads.TasksIdTasks = TmpTasks.id
                JOIN        Projects ON TmpTasks.ProjectsIdProjects = Projects.id
                LEFT JOIN   Users CotechOperationalUsers ON Projects.ProjectsCotechOperational = CotechOperationalUsers.id
                LEFT JOIN   Users SuperviseurUsers ON Projects.ProjectsCotechSupervisor = SuperviseurUsers.id
                JOIN        Customers ON Projects.CustomersIdCustomers = Customers.Id
                WHERE       TasksLoads.JobsIdJobs = '".$jobsid."'";
        if ($intext == 'internal') {
            $sql .= "
                AND         TasksLoads.ProvidersIdProviders IS NULL
            ";
        }
        if ($intext == 'external') {
            $sql .= "
                AND         TasksLoads.ProvidersIdProviders IS NOT NULL
            ";
        }
        $sql .= "
                AND         Projects.ProjectsIsTrashed = 0
                AND         Projects.ProjectsSuccess > 0
                AND         (
                                (TmpTasks.TasksBeginAt >= '".$date->format('Y/m/d')."' AND TmpTasks.TasksBeginAt < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK))
                                OR
                                (TmpTasks.TasksEndAt >= '".$date->format('Y/m/d')."' AND TmpTasks.TasksEndAt < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK))
                                OR
                                (TmpTasks.TasksBeginAt <= '".$date->format('Y/m/d')."' AND TmpTasks.TasksEndAt > DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK))
                            )
                GROUP BY    CustomersName,
                            ProjectsName,
                            CotechOperationalUserName,
                            SuperviseurUserName";

        return $this->getEntityManager()
                        ->createNativeQuery($sql, $rsm)
                        ->getResult();
    }


    public function getChargeByJobsIdOnWeeks($jobsid, $date, $nb_week)
    {
        $rsm = new ResultSetMapping;

        $rsm->addScalarResult('week', 'week', 'integer');
        $rsm->addScalarResult('beginDate', 'beginDate', 'date');
        $rsm->addScalarResult('endDate', 'endDate', 'date');
        $rsm->addScalarResult('employees', 'employees', 'integer');
        $rsm->addScalarResult('daysOpen', 'daysOpen', 'integer');
        $rsm->addScalarResult('NbDaysOff', 'NbDaysOff', 'integer');


        $rsm->addScalarResult('DaysDispo', 'DaysDispo', 'float');

        $date_formated = $date->format('Y-m-d');
        $sql = <<<EVBUFFER_EOF
            SELECT
                    WEEKOFYEAR(DateAll.date) as week,
                    DateAll.date as beginDate,
                    DATE_ADD(DateAll.date, INTERVAL 6 DAY) as endDate

                    ,(
                    SELECT COUNT(id)
                    FROM    Users
                    WHERE   Users.UsersIdJobs = $jobsid
                    AND 	Users.enabled = 1
                    AND     Users.UsersBeginAt <= DateAll.date
                    AND     ( Users.UsersEndAt IS NULL OR Users.UsersEndAt >= DATE_ADD(DateAll.date, INTERVAL 5 DAY))
                    ) as employees

                    ,(
                    SELECT 5 - COUNT(id)
                    FROM DaysoffsGlobal
                    WHERE DaysoffsGlobal.DaysoffsGlobalDate >= DateAll.date
                    AND DaysoffsGlobal.DaysoffsGlobalDate <= DATE_ADD(DateAll.date, INTERVAL 5 DAY)
                    ) as daysOpen

                    ,(
                    SELECT COALESCE( FORMAT((SUM(Daysoffs.DaysoffsMorning) + SUM(Daysoffs.DaysoffsAfternoon)) / 2, 1), 0 )
                    FROM Daysoffs
                    LEFT JOIN Users ON Daysoffs.DaysoffsUsersId = Users.id
                    WHERE Daysoffs.DaysoffsDate >= DateAll.date
                    AND Daysoffs.DaysoffsDate <= DATE_ADD(DateAll.date, INTERVAL 5 DAY)
                    AND Users.UsersIdJobs = $jobsid
                    AND Users.enabled = 1
                    AND Users.UsersBeginAt <= DateAll.date
                    AND ( Users.UsersEndAt IS NULL OR Users.UsersEndAt >= DATE_ADD(DateAll.date, INTERVAL 5 DAY))
                    ) as NbDaysOff

                    ,(SELECT
                        employees * daysOpen - NbDaysOff
                    ) as DaysDispo

                FROM DateAll
                WHERE DateAll.date >= '$date_formated'
                AND  DateAll.date < DATE_ADD('$date_formated', INTERVAL $nb_week WEEK)
                GROUP BY week
EVBUFFER_EOF;

        return $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->getResult();

    }

    public function getInternalDispoDeChargeByJobsIdOnWeeks($jobsid, $date, $nb_week)
    {
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('week', 'week', 'integer');
        $rsm->addScalarResult('beginDate', 'beginDate', 'date');
        $rsm->addScalarResult('endDate', 'endDate', 'date');
        $rsm->addScalarResult('employes', 'employes', 'integer');
        $rsm->addScalarResult('daysOpen', 'daysOpen', 'integer');
        $rsm->addScalarResult('NbDaysOff', 'NbDaysOff', 'float');
        $rsm->addScalarResult('DaysDispo', 'DaysDispo', 'float');

        $sql = "SELECT  WEEKOFYEAR(DateAll.date) as week,
                        DateAll.date as beginDate,
                        DATE_ADD(DateAll.date, INTERVAL 4 DAY) as endDate,
                        NbUsers.Nb as employes,
                        COALESCE(
                            SUM(
                                CASE
                                    WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DaysoffsGlobal.id IS NULL) THEN
                                        1
                                    ELSE
                                        0
                                END
                            ), 0) as daysOpen,
                        COALESCE(DaysOff.NbDaysOff, 0)+COALESCE(DaysAbs.NbDaysAbs, 0) as NbDaysOff,
                        COALESCE(
                            SUM(
                                CASE
                                    WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DaysoffsGlobal.id IS NULL) THEN
                                        1
                                    ELSE
                                        0
                                END
                            ), 0)*NbUsers.Nb-COALESCE(DaysOff.NbDaysOff, 0)-COALESCE(DaysAbs.NbDaysAbs, 0) as DaysDispo
                FROM        DateAll
                JOIN        (
                                SELECT  COUNT(id) as Nb
                                FROM    Users
                                WHERE   Users.UsersIdJobs = '".$jobsid."'
                                AND     Users.UsersBeginAt <= DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                AND     COALESCE(Users.UsersEndAt, DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)) > DATE('".$date->format('Y/m/d')."')
                            ) NbUsers
                LEFT JOIN   DaysoffsGlobal ON DateAll.date = DaysoffsGlobal.DaysoffsGlobalDate
                LEFT JOIN   (
                                SELECT      WEEKOFYEAR(DateAll.date) as week,
                                            COALESCE(SUM(
                                                    CASE    WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND Daysoffs.id IS NOT NULL AND Daysoffs.DaysoffsMorning = TRUE AND Daysoffs.DaysoffsAfternoon = TRUE AND DaysoffsGlobal.id IS NULL) THEN
                                                                1
                                                            WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND Daysoffs.id IS NOT NULL AND Daysoffs.DaysoffsMorning = TRUE AND DaysoffsGlobal.id IS NULL) THEN
                                                                0.5
                                                            WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND Daysoffs.id IS NOT NULL AND Daysoffs.DaysoffsAfternoon = TRUE AND DaysoffsGlobal.id IS NULL) THEN
                                                                0.5
                                                            ELSE
                                                                0
                                                    END
                                            ), 0) as NbDaysOff
                                FROM        DateAll
                                LEFT JOIN   DaysoffsGlobal ON DateAll.date = DaysoffsGlobal.DaysoffsGlobalDate
                                JOIN        Daysoffs ON DateAll.date = Daysoffs.DaysoffsDate
                                JOIN        Users ON Daysoffs.DaysoffsUsersId = Users.id
                                WHERE       DateAll.date >= '".$date->format('Y/m/d')."'
                                AND         DateAll.date < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                AND         Users.UsersIdJobs = '".$jobsid."'
                                GROUP BY    week
                            ) DaysOff ON WEEKOFYEAR(DateAll.date) = DaysOff.week
                LEFT JOIN   (
                                SELECT      WEEKOFYEAR(DateAll.date) as week,
                                            COALESCE(SUM(
                                                CASE
                                                    WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DateAll.date < Users.UsersBeginAt AND DaysoffsGlobal.id IS NULL) THEN
                                                        1
                                                    WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DateAll.date > COALESCE(Users.UsersEndAt, DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)) AND DaysoffsGlobal.id IS NULL) THEN
                                                        1
                                                    ELSE
                                                        0
                                                END
                                            ), 0) as NbDaysAbs
                                FROM        DateAll
                                JOIN        Users
                                LEFT JOIN   DaysoffsGlobal ON DateAll.date = DaysoffsGlobal.DaysoffsGlobalDate
                                WHERE       DateAll.date >= '".$date->format('Y/m/d')."'
                                AND         DateAll.date < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                AND         Users.UsersIdJobs = '".$jobsid."'
                                AND         (
                                                (
                                                    Users.UsersBeginAt > '".$date->format('Y/m/d')."'
                                                AND Users.UsersBeginAt < COALESCE(Users.UsersEndAt, DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)) < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                                )
                                            OR
                                                (
                                                    COALESCE(Users.UsersEndAt, DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)) > DATE('".$date->format('Y/m/d')."')
                                                AND COALESCE(Users.UsersEndAt, DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)) < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                                )
                                            )
                                GROUP BY    week
                            ) DaysAbs ON WEEKOFYEAR(DateAll.date) = DaysAbs.week
                            
                WHERE       DateAll.date >= '".$date->format('Y/m/d')."'
                AND         DateAll.date < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                GROUP BY    week
                ORDER BY    week";

        return $this->getEntityManager()
                        ->createNativeQuery($sql, $rsm)
                        ->getResult();
    }

    public function getExternalDispoDeChargeByJobsIdOnWeeks($jobsid, $date, $nb_week)
    {
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('week', 'week', 'integer');
        $rsm->addScalarResult('beginDate', 'beginDate', 'date');
        $rsm->addScalarResult('endDate', 'endDate', 'date');
        $rsm->addScalarResult('prestataires', 'prestataires', 'integer');
        $rsm->addScalarResult('daysOpen', 'daysOpen', 'integer');
        $rsm->addScalarResult('NbDaysOff', 'NbDaysOff', 'float');
        $rsm->addScalarResult('DaysDispo', 'DaysDispo', 'float');

        $sql = "SELECT  WEEKOFYEAR(DateAll.date) as week,
                        DateAll.date as beginDate,
                        DATE_ADD(DateAll.date, INTERVAL 4 DAY) as endDate,
                        NbUsers.nb as prestataires,
                        COALESCE(SUM(
                            CASE WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DaysoffsGlobal.id IS NULL) THEN
                                1
                            ELSE
                                0
                            END
                        ), 0) as daysOpen,
                        COALESCE(DaysAbs.NbDaysAbs, 0) as NbDaysOff,
                        COALESCE(SUM(
                            CASE WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DaysoffsGlobal.id IS NULL) THEN
                                1
                            ELSE
                                0
                            END
                        ), 0)*NbUsers.nb-COALESCE(DaysAbs.NbDaysAbs, 0) as DaysDispo
                FROM        DateAll
                LEFT JOIN   DaysoffsGlobal ON DateAll.date = DaysoffsGlobal.DaysoffsGlobalDate
                JOIN        (
                                SELECT  COUNT(id) as Nb
                                FROM    Periods
                                WHERE   Periods.idjob = '".$jobsid."'
                                AND     Periods.startdate <= DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                AND     Periods.enddate > DATE('".$date->format('Y/m/d')."')
                            ) NbUsers
                LEFT JOIN   (
                                SELECT      WEEKOFYEAR(DateAll.date) as week,
                                            COALESCE(SUM(
                                                CASE
                                                    WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DateAll.date < Periods.startdate AND DaysoffsGlobal.id IS NULL) THEN
                                                        1
                                                    WHEN (DateAll.DayOfWeek IN (1, 2, 3, 4, 5) AND DateAll.date > Periods.enddate AND DaysoffsGlobal.id IS NULL) THEN
                                                        1
                                                    ELSE
                                                        0
                                                END
                                            ), 0) as NbDaysAbs
                                FROM        DateAll
                                JOIN        Periods
                                LEFT JOIN   DaysoffsGlobal ON DateAll.date = DaysoffsGlobal.DaysoffsGlobalDate
                                WHERE       DateAll.date >= '".$date->format('Y/m/d')."'
                                AND         DateAll.date < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                AND         Periods.idjob = '".$jobsid."'
                                AND         (
                                                (
                                                    Periods.startdate > '".$date->format('Y/m/d')."'
                                                AND Periods.startdate < Periods.enddate < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                                )
                                            OR
                                                (
                                                    Periods.enddate > DATE('".$date->format('Y/m/d')."')
                                                AND Periods.enddate < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                                                )
                                            )
                                GROUP BY    week
                            ) DaysAbs ON WEEKOFYEAR(DateAll.date) = DaysAbs.week
                WHERE       DateAll.Date >=  '".$date->format('Y/m/d')."'
                AND         DateAll.Date < DATE_ADD('".$date->format('Y/m/d')."', INTERVAL ".$nb_week." WEEK)
                GROUP BY    WEEKOFYEAR(DateAll.date)
        ";

        return $this->getEntityManager()
                        ->createNativeQuery($sql, $rsm)
                        ->getResult();
    }

}
