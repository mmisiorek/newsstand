<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 14.05.2017
 * Time: 14:48
 */

namespace AppBundle\Security;


use AppBundle\Entity\News;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NewsVoter extends Voter
{
    const DELETE = 'delete';
    const PUBLISH = 'publish';

    public function supports($attribute, $subject)
    {
        if(!in_array($attribute, array(self::DELETE, self::PUBLISH))) {
            return false;
        }

        if(!($subject instanceof News) && $subject !== 'news') {
            return false;
        }

        return true;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        switch($attribute) {
            case self::DELETE:
                /* @var $news News */
                $news = $subject;
                return $this->canDelete($news, $user);
            case self::PUBLISH:
                return $this->canPublish($user);
        }
    }

    public function canDelete(News $news, $user)
    {
        if(!($user instanceof User)) {
            return false;
        }

        return $news->getUser()->getId() == $user->getId();
    }

    public function canPublish($user)
    {
        return ($user instanceof User && $user->getIsVerified());
    }
}