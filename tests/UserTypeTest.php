<?php

namespace App\Tests\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Test\TypeTestCase;
use App\Entity\User;
use App\Form\UserType;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'username' => 'testuser',
            'password' => [
                'first' => 'password123',
                'second' => 'password123',
            ],
            'email' => 'test@example.com',
            'roles' => ['ROLE_USER'],
        ];
    
        $user = new User(); 
    
        $form = $this->factory->create(UserType::class, $user); 
    
        $form->submit($formData);
    
        $this->assertTrue($form->isSynchronized());
        $this->assertInstanceOf(User::class, $form->getData());
    }
    
    
}


