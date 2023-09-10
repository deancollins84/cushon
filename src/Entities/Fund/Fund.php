<?php
namespace Entities\Fund;

class Fund
{
    public function __construct(
        protected string $name
    ){}

    public function getName(): string {
        return $this->name;
    }
}