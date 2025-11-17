FROM php:8.4-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    gnupg2 curl apt-transport-https apt-utils \
    unixodbc-dev libgssapi-krb5-2 \
    && rm -rf /var/lib/apt/lists/*

# Microsoft repo key + repo list
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc | \
    gpg --dearmor > /etc/apt/trusted.gpg.d/microsoft.gpg \
    && echo "deb [arch=amd64] https://packages.microsoft.com/debian/12/prod bookworm main" \
    > /etc/apt/sources.list.d/mssql-release.list

# Install Microsoft ODBC driver + tools
RUN apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 \
    && rm -rf /var/lib/apt/lists/*

# Install sqlsrv + pdo_sqlsrv via PECL
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Set working dir
WORKDIR /var/www/html