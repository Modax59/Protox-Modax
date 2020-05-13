<?php 

namespace App\Events;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class InvoiceChronoSubscriber implements EventSubscriberInterface {

    /**
     * @var UserPasswordEncoderInterface
     */
    private $security;
    private $repository;

    public function __construct(Security $security, InvoiceRepository $repository)
    {
        $this->security = $security;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setChronoForInvoice',EventPriorities::PRE_VALIDATE]
        ];
    }

    public function setChronoForInvoice(ViewEvent $event)
    {

        $invoice = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();


        if($invoice instanceof Invoice && $method === "POST")
       {
           $invoice->setChrono($this->repository->findNextChrono($this->security->getUser()));
           
           //TODO A DEPLACER DANS SA CLASSE 
           if(empty($invoice->getSentAt())){
               $invoice->setSentAt(new \DateTime());
            }
        }
    }
}