# Flashbox Marketplace System

This is an backend api application which is created as an assignment to be recruited in Flashbox Co and have some api to order a product which is nearby the user based on user address lat & lng.

This project is written by lumen and all tasks developed with Gitflow architecture. Also the project is Dockerized and all configurations files are included in the project. Of course all api routes are followed REST guidelines.

Almost all services of lumen is used: Model, Migration, Seeder, Policy, Gate, Controller, Route, Resource, Middleware, ...

It is good to mention that this project is developed in just 10 hours.

### Getting Started

To get started please follow these steps:

- Install docker engine on your os
- cd into project folder in cli
- copy .env.example to .env file then use these preferred configuration:

```
APP_NAME=Flashbox
APP_URL=http://localhost:8010

DB_HOST=flashbox_db
DB_PORT=3306
DB_DATABASE=flashbox
DB_USERNAME=root
DB_PASSWORD=root

BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=flashbox_redis
REDIS_PASSWORD=null
REDIS_PORT=6379

JWT_SECRET=
JWT_TTL=null
JWT_REFRESH_TTL=null
```

- docker-compose build
- docker-compose up -d
- docker-compose exec flashbox_app bash
- ./startup.sh
- php artisan jwt:secret

Yes! Now your backend api application is up and running on `http://localhost:8010`

Note: you need `docker-compose build` just for the first time. for the next times that you want to start the docker, do not use it

### Git

The developing process followed gitflow structure, So I've included the .git folder in the repository for you to check the logs. So please check it out.

#### ToDo

This project can be improved by adding followings:

- Swagger
- Tests

### Contact

This is Vahid Ramezanipour who developed this project with more than 7 years developing experience background as a full stack developer whom main focus was on backend side.

Email: vahidramezanipour@gmail.com

Linkedin: https://www.linkedin.com/in/vahidramezanipour

Website: https://ramezanipour.com
