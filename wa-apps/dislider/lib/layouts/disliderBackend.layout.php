<?php

class disliderBackendLayout extends waLayout
{
    public function execute(){
        $this->executeAction('sidebar', new disliderBackendSidebarAction());
        $this->executeAction('options', new disliderBackendOptionsAction());
    }
}