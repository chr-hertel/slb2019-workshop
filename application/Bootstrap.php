<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initTranslate()
    {
        Zend_Registry::set('Zend_Locale', new Zend_Locale('de_DE'));

        Zend_Registry::set('Zend_Translate', new Zend_Translate([
            'adapter' => 'xliff',
            'content' => realpath(dirname(__FILE__).'/../language/'),
        ]));
    }

    protected function _bootstrap($resource = null)
    {
        parent::_bootstrap($resource);

        if (null === $resource) {
            TravelOrganizer_Auth_UserProvider::init();
        }
    }
}
