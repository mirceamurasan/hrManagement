<?php

namespace FullSix\ProjectForecastBundle\Listener;

use Gedmo\Loggable\LoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Gedmo\Tool\Wrapper\AbstractWrapper;

class PfLoggableListener extends LoggableListener
{
    /**
     * Create a new Log instance
     *
     * @param string $action
     * @param object $object
     * @param LoggableAdapter $ea
     * @return void
     */
    protected function createLogEntry($action, $object, LoggableAdapter $ea)
    {
        $om = $ea->getObjectManager();
        $wrapped = AbstractWrapper::wrap($object, $om);
        $meta = $wrapped->getMetadata();
        if ($config = $this->getConfiguration($om, $meta->name)) {
            $logEntryClass = $this->getLogEntryClass($ea, $meta->name);
            $logEntryMeta = $om->getClassMetadata($logEntryClass);
            $logEntry = $logEntryMeta->newInstance();

            $logEntry->setAction($action);
            $logEntry->setUsername($this->username);
            $logEntry->setObjectClass($meta->name);
            $logEntry->setLoggedAt();

            // check for the availability of the primary key
            $objectId = $wrapped->getIdentifier();
            if (!$objectId && $action === self::ACTION_CREATE) {
                $this->pendingLogEntryInserts[spl_object_hash($object)] = $logEntry;
            }
            $uow = $om->getUnitOfWork();
            $logEntry->setObjectId($objectId);
            $newValues = array();
            $oldValues = array();
            if ($action !== self::ACTION_REMOVE && isset($config['versioned'])) {
                foreach ($ea->getObjectChangeSet($uow, $object) as $field => $changes) {
                    if (!in_array($field, $config['versioned'])) {
                        continue;
                    }
                    $value = $changes[1];
                    if ($meta->isSingleValuedAssociation($field) && $value) {
                        $oid = spl_object_hash($value);
                        $wrappedAssoc = AbstractWrapper::wrap($value, $om);
                        $value = $wrappedAssoc->getIdentifier(false);
                        if (!is_array($value) && !$value) {
                            $this->pendingRelatedObjects[$oid][] = array(
                                'log' => $logEntry,
                                'field' => $field
                            );
                        }
                    }
                    $newValues[$field] = $value;
                    if ($logEntryClass == 'FullSix\ProjectForecastBundle\Entity\PfLogEntries') {
                        $value = $changes[0];
                        if ($meta->isSingleValuedAssociation($field) && $value) {
                            $oid = spl_object_hash($value);
                            $wrappedAssoc = AbstractWrapper::wrap($value, $om);
                            $value = $wrappedAssoc->getIdentifier(false);
                            if (!is_array($value) && !$value) {
                                $this->pendingRelatedObjects[$oid][] = array(
                                    'log' => $logEntry,
                                    'field' => $field
                                );
                            }
                        }
                        $oldValues[$field] = $value;
                    }
                }
                $logEntry->setData($newValues);
                if ($logEntryClass == 'FullSix\ProjectForecastBundle\Entity\PfLogEntries') {
                    $logEntry->setOldData($oldValues);
                }
            }

            if($action === self::ACTION_UPDATE && 0 === count($newValues)) {
                return;
            }

            $version = 1;
            if ($action !== self::ACTION_CREATE) {
                $version = $ea->getNewVersion($logEntryMeta, $object);
                if (empty($version)) {
                    // was versioned later
                    $version = 1;
                }
            }
            $logEntry->setVersion($version);

            $this->prePersistLogEntry($logEntry, $object);

            $om->persist($logEntry);
            $uow->computeChangeSet($logEntryMeta, $logEntry);
        }
    }
}
