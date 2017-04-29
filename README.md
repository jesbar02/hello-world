# Simple-web-app 

This project is about Deploying a simple app that outputs the list of employees that are Male which birth date is 1965-02-01 and the hire date is greater than 1990-01-01 ordered by the Full Name of the employee.

For the present task, we are going to work with the following pre-build images released for the corresponding contributor:

#### Mysql Server: [Official Repository](https://hub.docker.com/_/mysql/).
#### Ubuntu 16.04: [Official Repository](https://hub.docker.com/_/ubuntu/).
In both cases, there are Supported tags and respective Dockerfile links. 

### Downloading dockerized mysql-server.
This procedure downloads the latest maintained version:

```html
docker pull mysql/mysql-server:latest
```

### Initializing new instance mapping volumes on the host machine.
This choice would give us the possibility to store the data generated inside the instance, in case of something goes wrong  with the container or we delete it accidentally, we can still have the data.

```html
docker run -d -v /testmysql:/var/lib/mysql -v /mysql-datadir/test_db:/test_db \
-e MYSQL_ROOT_PASSWORD=mypassword --name test-mysql mysql/mysql-server
```

We can confirm the status of the running container with: docker ps 
```html
CONTAINER ID        IMAGE                          COMMAND                  CREATED             STATUS              PORTS                         NAMES
8e6cbdbd52d3        mysql/mysql-server             "/entrypoint.sh my..."   12 hours ago        Up 12 hours         3306/tcp, 33060/tcp           test-mysql
```

### Downloading DB.
The needed DBs are available at: https://github.com/datacharmer/test_db. 
Start Downloading repository from any path on the host machine, we must to initialize the project directory. We must to create new directory where we are going to clone the project:
```html
$ mkdir testdb

- Entering and initializing repository
$ cd testdb/
/testdb$ git init 
Initialized empty Git repository in /home/jesushb/testdb/.git/

- Cloning from github’s repo page
~/testdb$ git clone https://github.com/datacharmer/test_db.git
Cloning into 'test_db'...
remote: Counting objects: 94, done.
remote: Total 94 (delta 0), reused 0 (delta 0), pack-reused 94
Unpacking objects: 100% (94/94), done.
Checking connectivity... done.
```

After that we can move the hole directory to the mapped volume /mysql-datadir/test_db/ in order to recreate the database inside the container.
```html
- Making directory
mkdir -p /mysql-datadir/test_db/

- Getting inside directory
cd /mysql-datadir/test_db/

- Coping database dump to directory
cp -R /mysql-datadir/test_db/ .

Finally we have required files for the starting of the database inside container:
Changelog                      employees.sql          load_dept_emp.dump      load_salaries1.dump  load_titles.dump  sakila            test_employees_md5.sql
employees_partitioned_5.1.sql  images                 load_dept_manager.dump  load_salaries2.dump  objects.sql       show_elapsed.sql  test_employees_sha.sql
employees_partitioned.sql      load_departments.dump  load_employees.dump     load_salaries3.dump  README.md         sql_test.sh
```
### Getting inside container.
```html
docker exec -it test-mysql bash
```
From inside the container, we must move to the working directory. Once there we can create employees database as follow:
mysql < employees.sql

### Accesing mysql CLI. 
We can start a new CLI session either from inside the container or from the host machine:
```html
docker exec -it test-mysql mysql -uroot -pmypassword
```

After this, we create a new use who we are going to use to call database using the PHP script.
```html
- Creating user 'user', with permission to access from any host
mysql> CREATE USER 'user'@'%' IDENTIFIED BY 'password';

- Giving "user"  account priviledges on employees database:
mysql> GRANT select, insert, update, delete, index, alter, create, drop ON employees.* TO 'user'@'%' identified by 'password';

This is the query to find the users that match requirement :
- Selecting database:
mysql> use employees;

- Testing query:
mysql> SELECT first_name,
		       last_name 
		FROM   employees 
		WHERE  gender = 'M' 
       		   AND birth_date = '1965-02-01' 
       		   AND `hire_date` > '1990-01-01' 
		ORDER  BY first_name; 
+------------+-----------+
| first_name | last_name |
+------------+-----------+
| Chiranjit  | Dredge    |
| Dannz      | Zhang     |
| Fun        | Seiwald   |
| Henk       | Anger     |
| Hiroyasu   | Provine   |
| Kagan      | Dredge    |
| Koldo      | Luit      |
| Make       | Olivero   |
| Snehasis   | Muhlberg  |
+------------+-----------+
9 rows in set (0.20 sec)
```
Now that we are running a working mysql docker image container, we proceed to create the container which is going to become the test environment for the web application to connect against our current mysql docker instance.

### Building web application instance.
Docker give us the chance to deploy a new container from a existing local or remote repository using a DockerFile (Uploaded into the present repository). 

Note that the we also use default, start.sh and supervisord.conf files located in the same directory. Just create a new directory and put all that files inside of it. Enter inside the directory and finally proceed with the build:
```html
docker build -t nginx_php7-fpm_mysql-support .
```

### Link the webapp to the mysql-server container.
Fist, Be care to create /webroot and put inside at least the need (in this case) results.php script. In order to connect the webapp container to the mysql server, we can enter the following code:
```html
docker run -d -v /webroot:/var/www/html -p 80:80 --link test-mysql:mysql/mysql-server \
--name webapp nginx_php7-fpm_mysql-support
```

Again, we use the -v option that let us map volumes between host and container. We would retrieve this information confirming that the new container is running:
```html
CONTAINER ID        IMAGE                          COMMAND                  CREATED             STATUS              PORTS                         NAMES
2cf88d2dd56f        nginx_php7-fpm_mysql-support   "./start.sh"             13 hours ago        Up 13 hours         0.0.0.0:80->80/tcp, 443/tcp   webapp
```

It just rests to test our application from a web browser at the local host and confirm that everything is working as expected by entering http://localhost/results.php.



Note: 
The required files are in the present repository.


