<?php
/* (c) 2015 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\Admin;

use HSMA\InfoDisplay\Controller\TimetableCache;
use HSMA\InfoDisplay\Entity\Domain\CancelledEvent;
use HSMA\InfoDisplay\Entity\Domain\NewsEntry;
use HSMA\InfoDisplay\Entity\Form\LecturerSelectionForm;
use HSMA\InfoDisplay\Entity\Form\NewsEntryForm;
use HSMA\InfoDisplay\Entity\Domain\TimeTableEntry;
use HSMA\InfoDisplay\Persistency\RESTClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CancellationController
 * @package HSMA\InfoDisplay\Controller\Admin
 *
 * Post news to the info-display.
 */
class CancellationController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    public function newAction(Request $request) {

        $task = new LecturerSelectionForm();

        $form = $this->createFormBuilder($task)
            ->add('lecturer', 'entity',
                [
                    'class'       => 'HSMA\InfoDisplay\Entity\Domain\Lecturer',
                    'required'    => false,
                    'label'       => 'Dozent',
                    'empty_data'  => null,
                    'placeholder' => '----',
                ])
            ->add('submit', 'submit', ['label' => 'Weiter'])
            ->add('cancel', 'submit', ['label' => 'Abbrechen'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            return $this->redirectToRoute('cancellation_add_for_lecturer',
                [ 'id' => $form->get('lecturer')->getData()->shortName ]);
        }
        else {
            return $this->render('InfoDisplayBundle:Admin:selectlecturer_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    public function addAction($id, Request $request) {

        $task = new NewsEntry();
        $task->valid = new \DateTime('Monday next week');
        $task->posted = new \DateTime();

        $cache = new TimetableCache($request->getSession());

        $timeTable = $cache->getTimetableForLecturer($id);

        $form = $this->createFormBuilder($task)
            ->add('valid', 'date',
                [
                    'label'  => 'Gültig bis',
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy',
                    'attr'   => [
                        'class'            => 'form-control input-inline datepicker',
                        'data-provide'     => 'datepicker',
                        'data-date'        => $task->valid->format('dd.MM.yyyy'),
                        'data-date-format' => 'dd.mm.yyyy'
                    ]
                ]
            )
            ->add('text', 'textarea',
                [
                    'label'    => 'Text der Nachricht',
                    'required' => false,
                    'attr'     => [
                        'cols'       => '80',
                        'rows'       => '2',
                        'novalidate' => 'novalidate',
                        'required'   => true,

                    ]
                ]
            )
            ->add('submit', 'submit', ['label' => 'Speichern'])
            ->add('cancel', 'submit', ['label' => 'Abrechen'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $newsId = $task->id;

            $cancelled = $request->request->get('cancelled');

            if (isset($cancelled)) {
                foreach ($cancelled as $id) {
                    $c = new CancelledEvent();
                    $c->newsId = $newsId;
                    $c->posted = $task->posted;
                    $c->valid = $task->valid;
                    $c->timetableKey = $id;
                    $em->persist($c);
                }
            }

            $em->flush();

            return $this->forward('InfoDisplayBundle:Admin\Cancellation:list');
        }
        else {
            return $this->render('InfoDisplayBundle:Admin:editcancellation_form.html.twig', array(
                'form' => $form->createView(),
                'timetable' => $timeTable,
            ));
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request) {

        $task = new NewsEntryForm();
        $task->valid = new \DateTime('Monday next week');

        $form = $this->makeForm($task);

        $form->handleRequest($request);

        if ($form->get('load')->isClicked()) {
            if ($task->lecturer != null) {

                // Open repositories
                $repoTimeTable = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\TimeTableEntry');

                /** @var TimeTableEntry[] $timeTable */
                $timeTable = $repoTimeTable->findBy(['lecturer' => $task->lecturer->shortName],
                    ['dayOfWeek' => 'ASC']);

                $task->lecture = $timeTable;

                $form = $this->makeForm($task);

                return $this->render('InfoDisplayBundle:Admin:editnews_form.html.twig', array(
                    'form' => $form->createView(),
                    'timetable' => $timeTable,
                ));
            }
        }
        else {
            return $this->render('InfoDisplayBundle:Admin:editnews_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @param string|\Symfony\Component\Form\FormTypeInterface $task
     *
     * @return \Symfony\Component\Form\Form
     */
    private function makeForm($task) {
        $form = $this->createFormBuilder($task)
            ->add('valid', 'date', [
                'label'  => 'Gültig bis',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'attr'   => [
                    'class'            => 'form-control input-inline datepicker',
                    'data-provide'     => 'datepicker',
                    'data-date'        => $task->valid->format('dd.MM.yyyy'),
                    'data-date-format' => 'dd.mm.yyyy'
                ]]
            )
            ->add('text', 'textarea',
                [
                    'label'    => 'Text der Nachricht',
                    'required' => false,
                    'attr'     => [
                        'cols'       => '50',
                        'rows'       => '2',
                        'novalidate' => 'novalidate'
                    ]
                ]
            )
            ->add('lecturer', 'entity',
                [
                    'class'       => 'HSMA\InfoDisplay\Entity\Domain\Lecturer',
                    'required'    => false,
                    'label'       => 'Dozent',
                    'empty_data'  => null,
                    'placeholder' => '----',
                ])
            ->add('load',   'submit', ['label' => 'Vorlesungen laden'])
            ->add('submit', 'submit', ['label' => 'Speichern'])
            ->getForm();

        return $form;
    }

    public function listAction() {

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT u ' .
            '   FROM InfoDisplayBundle:Domain\CancelledEvent u ' .
            '      WHERE u.valid >= CURRENT_DATE()' .
            '   ORDER BY u.posted ASC');

        $cancellations = $query->getResult();

        $repoNews = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\NewsEntry');

        foreach ($cancellations as $cancelledEvent) {
            $news = $repoNews->find($cancelledEvent->newsId);
            $cancelledEvent->news = $news == null ? new NewsEntry() : $news;
        }

        return $this->render('InfoDisplayBundle:Admin:listcancellations_form.html.twig',
        [
            'cancellations' => $cancellations
        ]
        );
    }

    public function deleteAction($id) {
        $repo = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\CancelledEvent');
        $news = $repo->find($id);

        if ($news != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();
        }

        return $this->forward('InfoDisplayBundle:Admin\Cancellation:list');
    }
}
