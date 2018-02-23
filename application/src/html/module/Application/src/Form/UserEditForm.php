<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Form;


use Zend\Form\Element\File;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;
use Zend\Form\Form;

class UserEditForm extends Form {

    public function __construct($name = null) {
        parent::__construct('edit');

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