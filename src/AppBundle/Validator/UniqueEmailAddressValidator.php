<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 17:04
 */

namespace AppBundle\Validator;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailAddressValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $value \AppBundle\Entity\User */
        /* @var $user User */

        $userRepo = $this->em->getRepository('AppBundle:User');
        $user = $userRepo->findOneBy(array('email' => $value->getEmail()));

        if($user && $user->getIsVerified()) {
            $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ email }}', $value->getEmail())
                        ->addViolation();
        }
    }
}