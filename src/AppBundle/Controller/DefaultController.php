<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Ticket;
use AppBundle\Form\ClientType;
use AppBundle\Form\ReservationType;
use AppBundle\Form\TicketType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */

    public function indexAction(Request $request) {

        return $this->render('default/index.html.twig');

    }

    public function billetterieAction(Request $request) {

        $reservation = new Reservation();

        $reservationForm = $this->get('form.factory')->create(ReservationType::class, $reservation);

        return $this->render('default/billetterie.html.twig', array(

            'reservation'          => $reservation,
            'reservationForm'      => $reservationForm->createView(),

        ));

    }

    public function checkNbTicketsParDateAction(Request $request) {

        if($request->isXmlHttpRequest() && $request->isMethod('POST')) {

            /*                  Récupération données AJAX                   */

            $datepickerValue = $_POST['datepickerValue'];

            /*                  Récupération du nombre de tickets journalier                    */

            $nbTicketsJournaliers = $this->getDoctrine()->getRepository('AppBundle:Reservation')->getNbTicketsJournalier($datepickerValue);

            /*                  Réponse AJAX                    */

            $response = new JsonResponse();

            $response->setData(array(

                'nbTicketsJournaliers' => $nbTicketsJournaliers

            ));

            return $response;

        }

    }

    public function addReservationAction(Request $request) {

        $reservation = new Reservation();

        $reservationForm = $this->get('form.factory')->create(ReservationType::class, $reservation);

        $ticket = new Ticket();

        if($request->isMethod('POST') && $reservationForm->handleRequest($request)->isValid()) {

            $random = md5(uniqid(rand(10, 10), true));

            $ticket->setPrix(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);

            $reservation->setTicket($ticket);
            $reservation->setNumeroReservation($random);

            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('tickets', array(

                'id'                => $reservation->getId(),
                'ticketId'          => $ticket->getId()

            ));

        } else {

            $request->getSession()->getFlashBag()->add('notice', "erreur lors de l'envoi du formulaire..." );

        }

    }

    public function ticketsAction(Request $request, $id, $ticketId) {

        $reservation = $this->getDoctrine()->getRepository('AppBundle:Reservation')->find($id);
        $nbTickets = $reservation->getNombreTicket();

        $ticket = $this->getDoctrine()->getRepository('AppBundle:Ticket')->find($ticketId);

        $ticketForm = $this->get('form.factory')->create(TicketType::class, $ticket);

        if (null === $reservation) {

            throw new NotFoundHttpException("La réservation numéro ".$id." n'existe pas.");

        }

        return $this->render('default/tickets.html.twig', array(

            'reservation' => $reservation,
            'id'          => $reservation->getId(),
            'nbTickets'   => $nbTickets,
            'ticket'      => $ticket,
            'ticketForm'  => $ticketForm->createView(),
            'ticketId'    => $ticket->getId()

        ));

    }

    public function addClientAction(Request $request, $ticketId) {

        if($request->isXmlHttpRequest() && $request->isMethod('POST')) {

            $client = new Client();

            $ticket = $this->getDoctrine()->getRepository('AppBundle:Ticket')->find($ticketId);

            /*                  Récupération données AJAX                   */

            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $age = $_POST['age'];
            $nationalite = $_POST['nationalite'];
            $tarifReduit = $_POST['tarifReduit'];
            $prix  = $_POST['prix'];

            /*                  Traitement des données                  */

            if($nom != '' || isset($nom)) {

                $client->setNom($nom);

            }

            if($prenom != '' || isset($prenom)) {

                $client->setPrenom($prenom);

            }

            if($email != '' || isset($email) || filter_var($email, FILTER__VALIDATE_EMAIL)) {

                $client->setEmail($email);

            }

            if($age != '' && isset($age) || !is_nan($age)) {

                $client->setAge($age);

            }

            if($nationalite != '' && isset($nationalite)) {

                $client->setNationalite($nationalite);

            }

            if($tarifReduit != '' && isset($tarifReduit)) {

                if($tarifReduit == 'oui' || $tarifReduit == 'non') {

                    $client->setTarifReduit($tarifReduit);

                }

            }

            if($prix != '' && isset($prix) || !is_nan($prix)) {

                $client->setPrix($prix);

            }

            $client->setTicket($ticket);

            if($ticket->getPrix() > 0) {

                $nouveauPrix = $ticket->getPrix() + $prix;
                $ticket->setPrix($nouveauPrix);

            } else {

                $ticket->setPrix($prix);

            }

            /*                  Envoi des données en BDD                    */

           $em = $this->getDoctrine()->getManager();

            $em->persist($client);

            $em->flush();

            /*                  Réponse AJAX                    */

            $response = new JsonResponse();

            if(isset($nouveauPrix)) {

                $response->setData(array(

                    'successMessage' => 'Ticket ajouté avec succès',
                    'nouveauPrix'    => $nouveauPrix

                ));

            } else {

                $response->setData(array(

                    'successMessage' => 'Ticket ajouté avec succès',

                ));

            }

            return $response;

        }

    }

    public function paiementAction(Request $request, $id) {

        $reservation = $this->getDoctrine()->getRepository('AppBundle:Reservation')->find($id);
        $clientsToMail = $reservation->getTicket()->getClients();
        $ticket = $reservation->getTicket();

        $reservation->setPaye(true);
        $ticket->setPaye(true);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        foreach($clientsToMail as $client) {

            $nom = $client->getNom();
            $prenom = $client->getPrenom();
            $email = $client->getEmail();
            $prix = $client->getPrix();

            $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject('Votre ticket pour le Musée du Louvre')
                ->setFrom('billetterie@louvre-billetterie.romaingravier.fr')
                ->setTo($email)
                ->setBody(

                    $this->renderView('default/email.html.twig', array(

                        'reservation'  => $reservation,
                        'clientToMail' => $clientsToMail,
                        'ticket'       => $ticket,
                        'nom'          => $nom,
                        'prenom'       => $prenom,
                        'prix'         => $prix,

                    ))


                );

            $this->get('mailer')->send($message);

        }

        return $this->render('default/paiement.html.twig');

    }

}
