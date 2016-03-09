<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\Admin;

use HSMA\InfoDisplay\Entity\Security\PasswordChange;
use HSMA\InfoDisplay\Entity\Security\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * Class SecurityController
 * @package HSMA\InfoDisplay\Controller\Admin
 *
 * Login and password change.
 */
class SecurityController extends Controller {

    /**
     * Form for login
     */
    const LOGIN_FORM = 'InfoDisplayBundle:Security:login_form.html.twig';

    /**
     * Form for password changes.
     */
    const CHANGE_PASSWORD_FORM = 'InfoDisplayBundle:Security:change_password_form.html.twig';

    /**
     * Page for password changes done.
     */
    const CHANGE_PASSWORD_RESULT = 'InfoDisplayBundle:Security:change_password_result.html.twig';


    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * Display the login form. The validation is handled by the framework, therefore
     * no validation or processing present in this method.
     *
     * @param Request $request the request
     *
     * @return Response A Response instance
     */
    public function loginAction(Request $request) {

        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        }
        elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }
        else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        return $this->render(self::LOGIN_FORM, array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * Password changes.
     *
     * @param Request $request the request
     *
     * @return Response the response
     */
    public function changePasswordAction(Request $request) {

        $change = new PasswordChange();

        // create form inline instead of using a Type class because it is not very
        // likely that it will ever be reused
        $form = $this->createFormBuilder($change)
            ->add('oldPassword', 'password', array(
                'required' => true,
                'label'    => 'Altes Passwort'
            ))
            ->add('newPassword', 'password',
                array(
                    'required' => true,
                    'label'    => 'Neues Passwort'
                ))
            ->add('newPasswordRepeat', 'password',
                array(
                    'required' => true,
                    'label'    => 'Neues Password (Wiederholung)'
                ))
            ->add('submit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = password_hash($change->getNewPassword(), PASSWORD_BCRYPT, array('cost' => 12));
            $username = $this->get('security.token_storage')->getToken()->getUsername();

            $em = $this->getDoctrine()->getManager();

            /** @var User $user */
            $user = $em->getRepository('InfoDisplayBundle:Security\User')->find($username);
            $user->setPassword($hash);

            $em->flush();

            return $this->render(self::CHANGE_PASSWORD_RESULT, array());

        }
        else {
            return $this->render(self::CHANGE_PASSWORD_FORM, array(
                'form' => $form->createView(),
            ));
        }
    }
}
