#base image
FROM nginx:alpine
#copy the build 
COPY dev.vozo.xyz/ /usr/share/nginx/html/.
#
COPY nginx.conf /etc/nginx/
#expose
EXPOSE 80 443
#command
CMD ["nginx", "-g", "daemon off;"]


