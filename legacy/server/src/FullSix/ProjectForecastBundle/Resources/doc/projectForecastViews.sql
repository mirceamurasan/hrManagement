# Charges par taches
CREATE OR REPLACE VIEW `ViewTasksLoadsByTasks` AS
SELECT
    TasksLoads.TasksIdTasks
    , SUM(TasksLoads.TasksLoadsVolume) as TasksLoadsVolume
FROM TasksLoads
WHERE TasksLoads.isTrashed = FALSE
GROUP BY TasksLoads.TasksIdTasks
;
# Date de la tache non terminées la plus récente
CREATE OR REPLACE VIEW `ViewLastTasksLate` AS
SELECT
    Tasks.ProjectsIdProjects,
    MAX(Tasks.TasksEndAt) as TasksDate
FROM Tasks
LEFT JOIN States
    ON Tasks.StatesIdStates = States.Id AND States.StatesIsFinished <> 1
WHERE 
    Tasks.TasksEndAt < now()
GROUP BY Tasks.ProjectsIdProjects
;
# Nombres de tache et volume en retard
CREATE OR REPLACE VIEW `ViewTasksLate` AS
SELECT
    Tasks.ProjectsIdProjects,
    COUNT(Tasks.Id) as TasksNum,
    SUM(ViewTasksLoadsByTasks.TasksLoadsVolume) as TasksLoadsVolume
FROM Tasks
LEFT JOIN States
    ON Tasks.StatesIdStates = States.Id
LEFT JOIN ViewTasksLoadsByTasks
    ON Tasks.Id = ViewTasksLoadsByTasks.TasksIdTasks
WHERE 
    Tasks.TasksEndAt < now() 
AND
    States.StatesIsFinished <> 1
GROUP BY Tasks.ProjectsIdProjects
;
# Nombres de tache et volume futures
CREATE OR REPLACE VIEW `ViewTasksFuture` AS
SELECT
    Tasks.ProjectsIdProjects,
    COUNT(Tasks.Id) as TasksNum,
    SUM(ViewTasksLoadsByTasks.TasksLoadsVolume) as TasksLoadsVolume
FROM Tasks
LEFT JOIN States
    ON Tasks.StatesIdStates = States.Id
LEFT JOIN ViewTasksLoadsByTasks
    ON Tasks.Id = ViewTasksLoadsByTasks.TasksIdTasks
WHERE 
    Tasks.TasksEndAt >= now() 
AND
    States.StatesIsFinished <> 1
GROUP BY Tasks.ProjectsIdProjects
;
# Nombres de tache et volume passées
CREATE OR REPLACE VIEW `ViewTasksOld` AS
SELECT
    Tasks.ProjectsIdProjects,
    COUNT(Tasks.Id) as TasksNum,
    SUM(ViewTasksLoadsByTasks.TasksLoadsVolume) as TasksLoadsVolume
FROM Tasks
LEFT JOIN States
    ON Tasks.StatesIdStates = States.Id
LEFT JOIN ViewTasksLoadsByTasks
    ON Tasks.Id = ViewTasksLoadsByTasks.TasksIdTasks
WHERE 
    Tasks.TasksEndAt < now() 
AND
    States.StatesIsFinished = 1
GROUP BY Tasks.ProjectsIdProjects
;
# Projets en cours
CREATE OR REPLACE VIEW `ViewProjectsInProgress` AS
SELECT
    Agencies.AgenciesName,
    Customers.CustomersName,
    Projects.ProjectsName,
    CotechOp.username as CotechOpUsername,
    CotechSup.username as CotechSupUsername,
    CONCAT(ViewTasksOld.TasksNum, '-', ViewTasksOld.TasksLoadsVolume) AS TasksOld,
    CONCAT(ViewTasksFuture.TasksNum, '-', ViewTasksFuture.TasksLoadsVolume) AS TasksFuture,
    CONCAT(ViewTasksLate.TasksNum, '-', ViewTasksLate.TasksLoadsVolume) AS TasksLate,
    ViewLastTasksLate.TasksDate
FROM Projects
LEFT JOIN Customers
    ON Projects.CustomersIdCustomers = Customers.Id
LEFT JOIN Agencies
    ON Customers.AgenciesIdAgencies = Agencies.Id
LEFT JOIN Users CotechSup
    ON Projects.ProjectsCotechSupervisor = CotechSup.id
LEFT JOIN Users CotechOp
    ON Projects.ProjectsCotechOperational = CotechOp.id
LEFT JOIN ViewTasksOld
    ON Projects.Id = ViewTasksOld.ProjectsIdProjects
LEFT JOIN ViewTasksFuture
    ON Projects.Id = ViewTasksFuture.ProjectsIdProjects
LEFT JOIN ViewTasksLate
    ON Projects.Id = ViewTasksLate.ProjectsIdProjects
LEFT JOIN ViewLastTasksLate
    ON Projects.Id = ViewLastTasksLate.ProjectsIdProjects
WHERE Projects.ProjectsIsTrashed = FALSE AND (ViewTasksLate.TasksNum > 0 OR ViewTasksFuture.TasksNum > 0)
;
