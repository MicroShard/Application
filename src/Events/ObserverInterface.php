<?php

namespace Microshard\Application\Events;

interface ObserverInterface
{
    /**
     * @param Context $context
     * @return void
     */
    public function handleEvent(Context $context);
}