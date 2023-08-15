include .env
export

DC=docker-compose
MAIN_YML=-f docker-compose.yml

all:
build:
	$(DC) $(MAIN_YML) build
up:
	$(DC) $(MAIN_YML) up -d
up-logs:
	$(DC) $(MAIN_YML) up
down:
	$(DC) $(MAIN_YML) down
