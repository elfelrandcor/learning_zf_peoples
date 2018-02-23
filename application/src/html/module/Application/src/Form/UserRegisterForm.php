<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Form;


use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Password;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class UserRegisterForm extends Form {

    public function __construct($name = null) {
        parent::__construct('signUp');

        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'name',
            'type' => Text::class,
            'options' => [
                'label' => 'Name',
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
            'name' => 'confirm_password',
            'type' => Password::class,
            'options' => [
                'label' => 'Confirm Password',
            ],
        ]);
        $this->add([
            'name' => 'sex',
            'type' => Select::class,
            'options' => [
                'label' => 'Sex',
                'value_options' => [
                    1 => 'female',
                    'male',
                ],
            ],
        ]);
        $this->add([
            'name' => 'photo',
            'type' => File::class,
            'options' => [
                'label' => 'Photo',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
        ]);
    }
}