<?php

namespace Instudies\AdminBundle\Controller\Statistics;

use     Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
        Symfony\Component\HttpFoundation\Request
;

/**
 * @Route("/admin/statistics")
 */
class StatisticsController extends Controller
{
    /**
     * @Route("/", name="admin_statistics_default")
     */
    public function indexAction()
    {
        // redirect to default action - showing counters
        return  $this->forward('InstudiesAdminBundle:Statistics\Statistics:counters');
    }    
    
    /**
     * @Route("/counters", name="admin_statistics_counters")
     */
    public function countersAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        // group count
       $table['Группы'] = $em->createQuery(
                               "SELECT count(edu_group) 
                                FROM InstudiesSiteBundle:EducationGroup edu_group 
                                WHERE edu_group.deleted <> :deleted")
               
               ->setParameter('deleted', 1)
               ->getSingleScalarResult();  
        
        // active group count                
        $table['Активные группы'] = $em->createQuery(  
                               "SELECT count(edu_group)
                                FROM InstudiesSiteBundle:EducationGroup edu_group
                                WHERE SIZE(edu_group.users) >= :min_membership")
                
                ->setParameter('min_membership', 4)
                ->getSingleScalarResult();                
        
        
        // user count
        $table['Пользователи'] = $em->createQuery(
                               "SELECT count(user)
                                FROM InstudiesSiteBundle:User user
                                WHERE user.deleted <> :deleted")
                
                ->setParameter('deleted', 1)
                ->getSingleScalarResult();
        
        
        // active user count
        $table['Активные пользователи'] = count($em->createQuery(
                              "SELECT DISTINCT user, user_edu_group
                               FROM InstudiesSiteBundle:UserEducationGroup user_edu_group 
                                JOIN user_edu_group.user user                                      
                               WHERE user.emailActivated = 1 AND
                                user.filledAllInformation = 1 AND
                                user.lastVisit IS NOT NULL AND 
                                user.lastVisit >= :maxPeriod AND
                                user_edu_group.educationGroup IN
                                    (SELECT edu_group
                                    FROM InstudiesSiteBundle:EducationGroup edu_group
                                    WHERE SIZE(edu_group.users) >= :min_membership)")
                
                        ->setParameter('min_membership', 4)
                        ->setParameter('maxPeriod', date("Y-M-d", strtotime("-2 months")))
                        ->execute()); 
        
        
        // homework amount
        $table['Домашние задания'] = $em->createQuery(
                              "SELECT count(homework) 
                               FROM InstudiesSiteBundle:EducationGroupHomeworkPost homework")
                
                ->getSingleScalarResult();
        
        
        // summary count
        $table['Конспекты'] = $em->createQuery(
                              "SELECT count(summary) 
                               FROM InstudiesSiteBundle:EducationGroupSummaryPost summary")
                
                ->getSingleScalarResult();
        
        
        // number of events posted
        $table['События'] = $em->createQuery(
                              "SELECT count(event) 
                               FROM InstudiesSiteBundle:EducationGroupEventPost event")
                
                ->getSingleScalarResult();
        
        
        // number of blog posts
        $table['Записи в блоге'] = $em->createQuery(
                              "SELECT count(blog_post) 
                               FROM InstudiesSiteBundle:EducationGroupBlogPost blog_post")
                
                ->getSingleScalarResult();
        
        
        // number of comments
        $table['Комментарии'] = $em->createQuery(
                              "SELECT count(comment) 
                               FROM InstudiesSiteBundle:Comment comment")
                
                ->getSingleScalarResult();
        
        
        // personal posts
        $table['Личные сообщения'] = $em->createQuery(
                              "SELECT count(message) 
                               FROM InstudiesSiteBundle:Message message")
                
                ->getSingleScalarResult();
        
        // reenter counter statistics
        return $this->render('InstudiesAdminBundle:Statistics:counters.html.twig', 
                              array('table' => $table));               
    }    
   
}
