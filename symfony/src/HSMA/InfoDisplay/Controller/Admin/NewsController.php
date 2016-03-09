<?php
/* (c) 2015 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\Admin;

use HSMA\InfoDisplay\Controller\Config;
use HSMA\InfoDisplay\Entity\Domain\NewsEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NewsController
 * @package HSMA\InfoDisplay\Controller\Admin
 *
 * Post news to the info-display.
 */
class NewsController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id = null, Request $request) {

        if (isset($id) && $id != null) {
            $repo = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\NewsEntry');
            $task = $repo->find($id);
        }
        else {
            $task = new NewsEntry();
            $task->valid = new \DateTime('Monday next week');
            $task->posted = new \DateTime();
        }

        $form = $this->makeForm($task);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->get('submit')->isClicked()) {

                if (strlen($form->get('text')->getData()) == 0) {
                    $form->addError(new FormError('Text muss angegeben werden'));

                    return $this->render('InfoDisplayBundle:Admin:editnews_form.html.twig', array(
                        'form' => $form->createView(),
                    ));
                }

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($task);
                $manager->flush();
            }

            return $this->redirectToRoute('news_list');
        }
        else {
            return $this->render('InfoDisplayBundle:Admin:editnews_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    public function listCurrentNewsAction() {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT u ' .
            '   FROM InfoDisplayBundle:Domain\NewsEntry u ' .
            '      WHERE u.valid >= CURRENT_DATE()' .
            '   ORDER BY u.posted ASC');
        $news = $query->getResult();

        return $this->render('InfoDisplayBundle:Admin:listnews_form.html.twig', array(
            'news' => $news,
        ));
    }

    public function listOldNewsAction() {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT u ' .
            '   FROM InfoDisplayBundle:Domain\NewsEntry u ' .
            '      WHERE u.valid <= CURRENT_DATE()' .
            '   ORDER BY u.posted ASC');
        $news = $query->getResult();

        return $this->render('InfoDisplayBundle:Admin:listnews_form.html.twig', array(
            'news' => $news,
        ));
    }

    public function deleteAction($id) {
        $repo = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\NewsEntry');
        $news = $repo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($news);

        $repo = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\CancelledEvent');
        $cancellations = $repo->findBy([ 'newsId' => $id ]);

        foreach ($cancellations as $cancellation) {
            $em->remove($cancellation);
        }

        $em->flush();

        return $this->redirectToRoute('news_list');
    }


    /**
     * @param string|\Symfony\Component\Form\FormTypeInterface $task
     *
     * @return \Symfony\Component\Form\Form
     */
    private function makeForm($task) {
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
                    'required' => true,
                    'attr'     => [
                        'cols'       => '80',
                        'rows'       => '2',
                        'novalidate' => 'novalidate'
                    ]
                ]
            )
            ->add('semester', 'choice',
                [
                    'choice_list' => new ChoiceList(Config::ALL_SEMESTERS, Config::ALL_SEMESTERS),
                    'required'    => false,
                    'label'       => 'Semester',
                    'empty_data'  => null,
                    'placeholder' => '----',
                ])
            ->add('submit', 'submit', ['label' => 'Speichern'])
            ->add('cancel', 'submit', ['label' => 'Abrechen'])
            ->getForm();

        return $form;
    }
}
