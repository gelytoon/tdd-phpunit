<?php

use \PHPUnit\Framework\TestCase;
require_once __DIR__."/../../src/controllers/UserAuth.php";

/* 
 * Creen una clase REAL de Autenticación.
 *
 * 1. Registrar Usuarios - username, password, email.
 * 2. Hash Password - Blowfish / Bcrypt (password_hash).
 * 3. Iniciar Sesion - email, password.
 */

class userAuthTest extends TestCase 
{
    /**
     * @test 
     * @dataProvider userRegister
     */
    public function register(string $username, string $password, string $email, bool $expected) {
        $user = new userAuth();

        /* //Si no comprobáramos previamente la existencia del email en la bd
        $this->expectException(mysqli_sql_exception::class);
        $this->expectExceptionMessage("Duplicate entry"); */

        //con Mock
        /* $user = $this->getMockBuilder(userAuth::class)->getMock();
        $user->method("register")->with($username, $password, $email)->willReturn($expected); */
        
        $registro = $user->register($username, $password, $email);
        $this->assertEquals($registro, $expected);
    }

    public static function userRegister() {
        return [
            ['usuario1', '1234', 'email@valido.es', true],
            ['usuario2', '4321', 'otro@valido.es', true],
            ['usuario3', '0000', 'email@valido.es', false]   //email ya existente en la bd
        ];
    }

    /**
     * @test 
     * @dataProvider userLogin
     */
    public function login(string $email, string $password, bool $expected){
        $user = new userAuth();

        $login = $user->login($email, $password);

        $this->assertEquals($login, $expected);
    }

    public static function userLogin() {
        return [
            ['email@valido.es', 'InvalidPassword', false],  // email existente, password errónea
            ['otro@valido.es', '4321', true],               // email existente, password correcta
            ['email@inexistente.es', 'anypwd', false]       // email inexistente, cualquier password
        ];
    }
}

