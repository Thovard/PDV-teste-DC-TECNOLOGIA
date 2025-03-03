# Projeto de Avaliação - DC TECNOLOGIA

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql)

Projeto desenvolvido por **Eduardo Trevezani** - Desenvolvedor com mais de 6 anos de experiência - como avaliação técnica para a vaga de desenvolvedor na DC TECNOLOGIA.

---

## 📋 Pré-requisitos

- PHP 8.2+
- Composer 2.6+
- Node.js 18.x+
- MySQL 8.x+
- Git

---

## 🛠️ Tecnologias Utilizadas

### Principais Dependências
- Laravel Framework 12.x
- Laravel Fortify 1.x (Autenticação)
- Laravel Tinker 2.x

### Dev Dependencies
- Pest PHP (Testing)
- Laravel Sail (Docker)
- Laravel Pint (Code Style)

---

## ⚙️ Configuração do Projeto

### 1. Clonar repositório
```
git clone https://github.com/Thovard/PDV-teste-DC-TECNOLOGIA.git

```
### Instalar dependências
```
composer install
npm install
cp .env.example .env
```

### Editar variáveis de banco de dados no .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=root
DB_PASSWORD=
```

### Banco de dados
```
php artisan migrate --seed
```
### Gerar chave da aplicação
```
php artisan key:generate
```
🚀 Executando a Aplicação
Iniciar servidor de desenvolvimento:

```
composer run dev
```
A aplicação estará disponível em:
[Local Host](http://127.0.0.1:8000)

🔑 Credenciais de Teste
### Usuário administrativo pré-cadastrado:
```
Email: admin@lintechdigital.com
Senha: 123456
```

📌 Notas Importantes
O sistema utiliza MySQL como banco de dados padrão

O comando composer run dev inicia simultaneamente:

Servidor Laravel

Queue Worker

Vite (frontend)

Seeders incluem o usuário padrão mencionado.
