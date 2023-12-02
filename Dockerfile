# Use a base image with a web server, for example, Nginx
FROM nginx:latest

RUN git clone https://github.com/L00179719/mypipeline.git
RUN cd mypipeline

# Copy your index.html to the default Nginx web root directory
COPY index.html /usr/share/nginx/html/

# Expose port 80 to allow incoming connections
EXPOSE 80

# Start Nginx when the container starts
CMD ["nginx", "-g", "daemon off;"]