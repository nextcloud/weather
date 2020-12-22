FROM nextcloud:14.0.1-apache

COPY . /usr/src/nextcloud/apps/weather

RUN apt-get update -qy && \
	apt-get install --no-install-recommends -qy ca-certificates && \
	apt-get clean && \
        rm -rf /var/lib/apt/lists/*
