<?php
/**
 * @author Juriy Panasevich <elfelrandcor@gmail.com>
 */

namespace Application\Model;


use DomainException;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Filter\File\RenameUpload;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\UploadFile;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class User implements InputFilterAwareInterface {

    public $id, $name, $password, $sex, $photo, $rating, $vote = false;

    protected $dbAdapter;

    private $inputFilter;

    const SEX_FEMALE = 1;
    const SEX_MALE   = 2;

    public function __construct(AdapterInterface $adapter) {
        $this->dbAdapter = $adapter;
    }

    public function exchangeArray(array $data) {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
        $this->sex = !empty($data['sex']) ? $data['sex'] : null;
        $this->photo = !empty($data['photo']) ? $data['photo'] : null;
        $this->rating = !empty($data['rating']) ? $data['rating'] : 0;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
     * @throws \DomainException
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter() {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 15,
                    ],
                ],
                [
                    'name' => NoRecordExists::class,
                    'options' => [
                        'table' => 'user',
                        'field' => 'name',
                        'adapter' => $this->dbAdapter,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'password',
            'required' => true,
            'filters' => [],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 5,
                        'max' => 25,
                    ],
                ],
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => '/\d/',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'sex',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'photo',
            'required' => false,
            'filters' => [
                [
                    'name' => RenameUpload::class,
                    'options' => [
                        'target' => './public/photos',
                        'randomize' => true,
                        'use_upload_extension' => true,
                    ],
                ]
            ],
            'validators' => [
                ['name' => UploadFile::class],
                ['name' => IsImage::class],
                ['name' => FilesSize::class, 'options' => ['max' => '5MB']],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }

    public function getArrayCopy() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'password' => $this->password,
            'sex' => $this->sex,
            'photo' => $this->photo,
        ];
    }

    public function isVotedUp() {
        return $this->vote == \Rating\Model\RatingTable::DIRECTION_PLUS;
    }

    public function isVotedDown() {
        return $this->vote == \Rating\Model\RatingTable::DIRECTION_MINUS;
    }

    public function isMale() {
        return $this->sex == self::SEX_MALE;
    }

    public function isFemale() {
        return $this->sex == self::SEX_FEMALE;
    }
}