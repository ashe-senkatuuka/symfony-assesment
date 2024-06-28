
# symfony-assesment

  

Assesment for PHP symfony, Docker and static code analysis

  

## Introduction

  

This project is a RESTful API built using Symfony 6.4. It provides endpoints for managing products, including creating, retrieving, updating, and deleting products. The project uses Doctrine ORM for database interactions and JWT for authentication.

  

## Requirements

  

- Docker

  

- Docker Compose

  

- Git

  

## Setup Instructions

  

### 1. Build and Run Docker Containers

  

Build the Docker images

```bash

docker-compose  build

```

Start the Docker containers

```bash

docker-compose  up  -d

```

### 2. Set Up the Symfony Application

  

Enter the app container

```bash

docker  exec  -it  symfony_app  bash

```
Create .env from .env.example to and provide password to the database. Replace !ChangeMe! with the password you create.

```bash
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"


```

Run database migrations

```bash

php  bin/console  doctrine:migrations:migrate

```

Clear the cache

```bash

php  bin/console  cache:clear  --env=dev
```
### 3. Static Code Analysis
Composer.json file contains a script "static-analysis" to run the static code analysis
```json
"static-analysis": "./run_static_analysis.sh"
```
Use the composer to execute the command
```bash
composer static-anaysis
```
Output of the static analysis and any fixes made will be generated and saved to the codebase.
The following files will be created: 

 1. static_analysis_report.txt
 2. phpstan_results.txt
 3. phpcs_results.txt
 4. code_fixes.diff
 
### .4  API documentation 
The api documentation can be accessed at `http://127.0.0.1/api/doc`

