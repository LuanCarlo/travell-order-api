Sobre o projeto
Essa é uma api desenvolvida utilizando Laravel (v12). Para executa-lo clone o repositório, entre na pasta do projeto (travell-order-api), execute comando composer intall, após instalação, php artisan migrate, se aparecer a mensagem "Would you like to create it? (yes/no)" digite yes, ao finalizar executar o comando php artisan:serve e a api já estará pronta para uso.

Atenção
A principais rotas estão protegidas então é necessário fazer o login ou cadastro de um usuário, ao realizar o login será retornado o token de autenticação, salvar no Athentication Barer Token, a utilização for ser realizada pelo portman/insominia, caso for utilizada em conjunto com a aplicação (front) travell-order-front seguir para as instruções teste. PS: Se as migrations tiverem sido executadas já existe um usuário cadastrado para testes (Login: admin@example.com, senha: password123)

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
