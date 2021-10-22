#### Setup Docker

1. cd into project folder
2. docker-compose up -d --build
3. docker exec -it flashbox_app bash
4. ./startup.sh
5. All Done! You can visit backend on `http://localhost:8010`

Note: you need --build flag just for the first time. for the next times that you want to start the docker, do not use it
