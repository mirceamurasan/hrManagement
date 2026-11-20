<?php
namespace FullSix\ProjectForecastBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FullSix\ProjectForecastBundle\Form\LoginType;
use FullSix\ProjectForecastBundle\Form\DataTransformer\LoginTransformer;

class AddLoginSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SUBMIT => 'postSetData');
    }

    public function postSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        $transformer = new LoginTransformer();
        $login = $user['login'];
        if ($login['username']) { // !== '' || $login['expiresAt'] !== '' || $login['plainPassword']['first'] !== null $login['plainPassword']['second'] !== null
            $form->add($this->factory->createNamed(
                    'login',
                    new LoginType(),
                    array('required' => true),
                    array('auto_initialize' => false)));
        }
    }
}

