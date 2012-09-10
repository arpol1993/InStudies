<?php

namespace Instudies\AdminBundle\Controller\Management;

use
        Symfony\Component\Form\FormError,
        Symfony\Component\Validator\Constraints\Email,
	Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
        Symfony\Component\HttpFoundation\Request
;

use
	Instudies\AdminBundle\Form\Management\User\Find\ManagementUserFindType,
        Instudies\AdminBundle\Form\Management\User\Edit\ManagementUserEditType
;

/**
 * @Route("/admin/management/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="admin_management_user")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        $findForm   = $this->createForm(new ManagementUserFindType());

        if ($request->getMethod() == 'POST') {
            $data = $findForm->bindRequest($request)->getData();
            if ($data['id']) {
                return $this->redirect($this->generateUrl('admin_management_user_edit_id', array('id' => intval($data['id']))));
            }
            else if (is_string($data['email'])) {
                return $this->redirect($this->generateUrl('admin_management_user_edit_email', array('email' => $data['email'])));
            }
            else 
            {
                $findForm->addError(new FormError("Укажите id пользователя или email", array()));                
            }
        }

    	return array('findForm' => $findForm->createView());

    }

    /**
     * @Route("/id/{id}/edit", name="admin_management_user_edit_id")
     * @Route("/email/{email}/edit", name="admin_management_user_edit_email")
     * @Template()
     */
    public function editAction($id = null, $email = null)
    {
        $userRepository = $this->getDoctrine()->getEntityManager()->getRepository('InstudiesSiteBundle:User');

        if ($id) {
            $user = $userRepository->findOneById(intval($id));
        } elseif ($email) {
            $user = $userRepository->findOneByEmail($email);
        }

        if (!$user instanceof \Instudies\SiteBundle\Entity\User) {
            throw $this->createNotFoundException('Такого пользователя не существует');
        }
        
        $editForm   = $this->createForm(new ManagementUserEditType());
        $editForm->setData($user);
        
        return array(   'editForm'  => $editForm->createView(),
                        'user'      => $user,
                        'id'        => ($user->getId()));
    }
    
    /**
     * @Route("/save/{id}", name="admin_management_user_save")
     */
    public function saveAction(Request $request, $id)
    {
        $saveForm = $this->createForm(new ManagementUserEditType());
        
        if ($request->getMethod() == 'POST') {
            $saveForm->bindRequest($request);
            
            $emailConstraint = new Email();
            $emailConstraint->message = 'Адрес електронной почты задан неверно';
            
            $errorList = $this->get('validator')->validateValue($saveForm->getData()->getEmail(), $emailConstraint);           
            
            if(count($errorList)) $saveForm->addError (new FormError($emailConstraint->message, array()));
            
            if(!$saveForm->hasErrors())
            { 
                $userRepository = $this->getDoctrine()->getEntityManager()->getRepository('InstudiesSiteBundle:User');
                
                $user = $userRepository->find($id);                
                
                $user->setFirstname($saveForm->getData()->getFirstname());
                $user->setLastname($saveForm->getData()->getLastname());
                $user->setEmail($saveForm->getData()->getEmail());
                $user->setEmailActivated($saveForm->getData()->getEmailActivated());
                $user->setFilledAllInformation($saveForm->getData()->getFilledAllInformation());
                $user->setPassword($saveForm->getData()->getPassword());                
                
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush(); 
                
                $name = $user->getFullname();
                $this->get('session')->setFlash('message',"Изменения пользователя $name успешно сохранены");
            }
            else
            { 
                return $this->render('InstudiesAdminBundle:Management\User:edit.html.twig', 
                        array(  'editForm'  => $saveForm->createView(),
                                'user'      => $saveForm->getData(),
                                'id'        => $id));               
            }
        }
        
        return $this->redirect($this->generateUrl('admin_management_user'), 301);
    }
    
    /**
     * @Route("/delete/{id}/{name}", name="admin_management_user_delete")
     * @Template()
     */
    public function deleteAction(Request $request, $id, $name)
    {
        if($request->getMethod() == 'POST'){
            
            $em = $this->getDoctrine()->getEntityManager();    
            
            $em->createQueryBuilder()->update('InstudiesSiteBundle:User', 'u')->
                    set('u.deleted', '1')->where('u.id = :id')->setParameter('id', $id)->
                    getQuery()->execute();             
            
            $this->get('session')->setFlash('message',"Пользователь $name был отмечен для удаления");
        }  
        
        return $this->redirect($this->generateUrl('admin_management_user'), 301);
    }
    
}
