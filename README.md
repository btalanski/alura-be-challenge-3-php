# Backend Challenge #3 - Alura

Implementação em PHP do Backend Challenge #3 da Alura. De 11 de abril a 10 de maio.

## Instalação da aplicação
Para executar a aplicação é preciso garantir que seu sistema atenda ao requisitos abaixo.

### Requisitos
- OS com suporte a Docker e Docker compose
- IDEs VS Code ou PHPStorm

### Instalação inicial
1. Clone o repositório
2. Crie um novo arquivo `.env` a partir do template e altera os valores conforme necessário:
```bash
    cd src && cp .env .env.local
```
3. Execute o docker-compose na raiz do diretório:
```bash
    docker-compose up -d
```
4. Instale os pacotes do composer dentro do container:
```bash
    # acessar o terminal dentro do container
    docker exec -it  challenge-php sh

    #instalação via composer
    composer install
```
5. Ainda dentro do container, execute as migrações para preparar o banco
```bash
    #cria o banco de dados para a aplicação
    php bin/console doctrine:database:create
    
    # faz o deploy das migrations
    php bin/console doctrine:migrations:migrate
```