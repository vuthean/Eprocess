# Base Image
FROM 10.80.80.148:5000/merchant-portal-base-image:0.0.1

# Set working directory
WORKDIR /var/www/html

# Copy application files first to leverage Docker caching
COPY . /var/www/html/

# Set up proxy (Optional: Uncomment and set your proxy)
ARG HTTP_PROXY="http://192.168.4.182:2020"
ARG HTTPS_PROXY="http://192.168.4.182:2020"
ENV HTTP_PROXY="http://192.168.4.182:2020"
ENV HTTPS_PROXY="http://192.168.4.182:2020"

# Remove composer.lock and install dependencies
RUN rm -f /var/www/html/composer.lock \
    && composer install \
    && composer dump-autoload
    
# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache public

# Enable Apache modules
RUN a2enmod rewrite ssl

# Configure PHP extensions
RUN echo "extension=gd\n\
extension=zip\n\
extension=ldap\n\
max_file_uploads=800\n\
post_max_size=500M\n\
upload_max_filesize=500M\n\
memory_limit=512M\n\
max_execution_time=600\n\
file_uploads=On" > /usr/local/etc/php/conf.d/custom-php.ini

# Copy Apache and SSL configuration
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf
COPY ./ssl/ /etc/apache2/ssl/

# Unset proxy environment variables after build is complete
ENV HTTP_PROXY=""
ENV HTTPS_PROXY=""

# Expose ports
EXPOSE 80 443