# kotm2020-ui 

copy the following files and rename by removing "-template" from the filename and edit credentials and hostnames accordingly
/source/assessments/api/config/database-template.php
/source/assessments/api/config/environment-template.php
/source/assessments/api/config/phpmailer-template.php
/source/volunteer-system/app/db/dao-template.php


dev enviroment requires docker and npm.

To get a dev environment up and running enter the following commands in a terminal
npm install
npm install -g gulp gulp-cli
gulp buildDev
docker-compose up
npm run dev

To get a production environment up and running enter the following commands in a terminal
npm install
npm install -g gulp gulp-cli
gulp buildProd

point your web server docroot to the /public folder and use the apache configuration found in /apache/000-default.conf