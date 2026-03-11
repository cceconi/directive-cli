<?php

/** @var string $projectName */
/** @var string $containerName */
return "FROM php:8.4-fpm\n\nARG WORKDIR=/web/" . $projectName . "\n\nRUN apt-get update && apt-get install -y \\\n    git \\\n    unzip \\\n    curl \\\n    && rm -rf /var/lib/apt/lists/*\n\n# Install Composer\nCOPY --from=composer:2 /usr/bin/composer /usr/bin/composer\n\nRUN mkdir -p \${WORKDIR}\n\nRUN groupadd -r appgroup && useradd -r -g appgroup appuser\n\nRUN chown -R appuser:appgroup \${WORKDIR}\n\nWORKDIR \${WORKDIR}\n\nUSER appuser\n\nEXPOSE 9000\n";
