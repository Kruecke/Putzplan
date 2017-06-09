.PHONY: all
all: build run

.PHONY: build
build:
	docker build -t putzplan .

.PHONY: run
run:
	docker run -d -p 80:80 putzplan
