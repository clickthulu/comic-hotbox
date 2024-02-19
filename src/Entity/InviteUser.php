<?php

namespace App\Entity;

class InviteUser
{

    protected ?string $userlist;

    /**
     * @return string|null
     */
    public function getUserlist(): ?string
    {
        return $this->userlist;
    }

    public function getUserArray(): array
    {
        $list = explode("\n", $this->userlist);
        $list = array_map('trim', $list);
        return $list;
    }

    /**
     * @param ?string $userlist
     */
    public function setUserlist(?string $userlist): InviteUser
    {
        $this->userlist = $userlist;
        return $this;
    }



}