<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Form;


use Zend\Form\Element\Captcha;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\Captcha\Dumb;

class LoginForm extends Form {

    public function __construct($name = null) {
        parent::__construct('login');

        $this->add([
            'name' => 'name',
            'type' => Text::class,
            'options' => [
                'label' => 'User name',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => Password::class,
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name' => 'captcha',
            'type' => Captcha::class,
            'options' => [
                'label' => 'Please verify you are human',
                'captcha' => new Dumb(),
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
        ]);
    }
}