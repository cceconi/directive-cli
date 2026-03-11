<?php

/** @var string $projectName */
/** @var string $containerName */
return "services:\n  " . $containerName . ":\n    build:\n      context: .\n      dockerfile: docker/Dockerfile\n    container_name: " . $containerName . "\n    volumes:\n      - .:/web/" . $projectName . "\n    environment:\n      - APP_ENV=dev\n    env_file:\n      - .env\n";
