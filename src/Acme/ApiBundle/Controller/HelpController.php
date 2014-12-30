<?php

namespace Acme\ApiBundle\Controller;

use Acme\ApiBundle\Event\ActionEvent;
use Acme\ApiBundle\Event\EventStore;
use Acme\EdelaBundle\Entity\Action;
use Acme\EdelaBundle\Entity\UserAction;
use Acme\EdelaBundle\Entity\UserActionProgress;
use Acme\EdelaBundle\Entity\UserSubactionProgress;
use Acme\EdelaBundle\Form\Type\ActionCreateShortFormType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Driver\Connection;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HelpController extends FOSRestController
{

    /**
     * @Rest\View
     * @Rest\Get("/help/static/{key}")
     */
    public function getStaticPageAction($key){
        $page = $this->getDoctrine()->getRepository('AcmeEdelaBundle:StaticPage')->findOneBy(['tkey' => $key]);
        if (!$page){
            throw $this->createNotFoundException();
        }
        return ['title' => $page->getTitle(), 'text' => $page->getText()];
    }

    /**
     * @Rest\View
     * @Rest\Get("/help/list")
     */
    public function getHelpListAction(Request $request)
    {

        $connection = $this->getDoctrine()->getConnection();

        return $connection->fetchAll('SELECT id, title FROM ed_help_topics WHERE is_enabled=:true', ['true' => 1]);

        return [
            ['id' => 1, 'title' => 'Изменение настроек оповещений e-dela.com'],
            ['id' => 2, 'title' => 'Выбор языка по умолчанию'],
        ];

    }

    /**
     * @Rest\View
     * @Rest\Get("/help/hot")
     */
    public function getHotHelpAction(Request $request)
    {
        /** @var Connection $connection */
        $connection = $this->getDoctrine()->getConnection();

        return $connection->fetchAll('SELECT question as title, answer FROM ed_help_questions WHERE is_hot=:true LIMIT 10', ['true' => 1]);


        return [
            ['title' => 'Для обеспечения безопасности ваших данных, Ответы имеют отдельную регистрацию от Ежедневные дела.', 'answer' => 'Регистрация займет 10 секунд и не потребует подтверждения Email.'],
            ['title' => 'Для обеспечения безопасности ваших данных2, Ответы имеют отдельную регистрацию от Ежедневные дела.', 'answer' => 'Регистрация займет 10 секунд и не потребует подтверждения Email.'],
            ['title' => 'Для обеспечения безопасности ваших данных3, Ответы имеют отдельную регистрацию от Ежедневные дела.', 'answer' => 'Регистрация займет 10 секунд и не потребует подтверждения Email.'],
            ['title' => 'Для обеспечения безопасности ваших данных4, Ответы имеют отдельную регистрацию от Ежедневные дела.', 'answer' => 'Регистрация займет 10 секунд и не потребует подтверждения Email.'],
        ];

    }

    /**
     * @Rest\View
     * @Rest\Get("/help/{id}")
     */
    public function getHelpAction(Request $request, $id)
    {
        /** @var Connection $connection */
        $connection = $this->getDoctrine()->getConnection();

        return $connection->fetchAssoc('SELECT id, title, text FROM ed_help_topics WHERE is_enabled=:true AND id=:id',['true' => 1, 'id' => $id]);
    }


}
