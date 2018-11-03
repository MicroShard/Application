<?php

namespace Application\Events;

interface ObserverInterface
{
    /**
     * @param Context $context
     * @return void
     */
    public function handleEvent(Context $context);
}