# Use an official PHP runtime as a parent image
FROM php:8.1-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Make port 80 available to the world outside this container
EXPOSE 80

# Run apache in foreground
CMD ["apache2-foreground"]