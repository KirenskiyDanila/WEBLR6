<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ValidatorBuilder;

class MyValidator
{
    public function getConstraints(): Assert\Collection
    {

        return new Assert\Collection([
            'name' => [
                new Assert\NotBlank(['message' => 'Название не должно быть пустым!'])
            ],
            'price' => [
                new Assert\NotBlank(['message' => 'Цена не должна быть пустой!']),
                new Assert\Regex(['pattern' => '/([1-9][0-9]*)+$/',
                    'message' => 'В поле Цена должно быть число'])
            ],
            'photo' => new Assert\File([
                'maxSize' => '12M',
                'maxSizeMessage' => '{{ name }} Максимальный размер файла - {{ limit }} {{ suffix }}',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                ],
                'mimeTypesMessage' => "{{ name }} Неверный тип файла {{ type }}."
            ])
        ]);
    }

    public function validate(array $data): array
    {
        $validator = Validation::createValidator();
        $constraints = $this->getConstraints();
        $violations = $validator->validate($data, $constraints);

        $errors = [];
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
        }
        return $errors;
    }
}
