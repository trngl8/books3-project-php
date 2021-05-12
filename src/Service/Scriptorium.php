<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\Member;

class Scriptorium
{
    public function borrow(Member $member, Card $card) : bool
    {
        if(!$card->getTitle()) {
            throw new \Exception(sprintf('Can not borrow card %s with empty title', $card->getId()));
        }

        if(!$member->getName()) {
            throw new \Exception(sprintf('Can not borrow card %s with empty member #%d name', $card->getId(), $member->getId()));
        }

        return true;
    }
}