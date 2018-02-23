<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Community\Form;


use Zend\Form\Element\Submit;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;

class CommentForm extends Form {

    public function __construct($name = null) {
        parent::__construct('comment');

        $this->add([
            'name' => 'text',
            'type' => Textarea::class,
            'options' => [
                'label' => 'Comment',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
        ]);
    }
}