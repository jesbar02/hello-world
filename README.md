
Deploy a simple app that outputs the list of employees that are Male which birth date is 1965-02-01 and the hire date is greater than 1990-01-01 ordered by the Full Name of the employee.

For the present task, we are going to work with the following pre-build images released for the corresponding contribu:   https://github.com/mysql/mysql-docker 

Mysql Server, OFFICIAL REPOSITORY
https://hub.docker.com/_/ubuntu/

Ubuntu 16.04, OFFICIAL REPOSITORY
https://hub.docker.com/_/ubuntu/

In both cases, there are Supported tags and respective Dockerfiles links. 

- Downloading dockerized mysql-server.
This procedure downloads the latest maintained version:
# docker pull mysql/mysql-server:latest

- Running docker mysql-server as a service for the first time and mapping host data directory. 
- Inicializing new instance mapping volumes on the host machine.
# docker run -d -v /testmysql:/var/lib/mysql -v /mysql-datadir/test_db:/test_db \
-e MYSQL_ROOT_PASSWORD=mypassword --name test-mysql mysql/mysql-server


CONTAINER ID        IMAGE                          COMMAND                  CREATED             STATUS              PORTS                         NAMES
8e6cbdbd52d3        mysql/mysql-server             "/entrypoint.sh my..."   12 hours ago        Up 12 hours         3306/tcp, 33060/tcp           test-mysql

- Accesing container CLI. 
docker exec -it test-mysql mysql -uroot -pmypassword

- Downloading DB.
The needed DBs are available at: https://github.com/datacharmer/test_db. 
Start Downloading repository from any path on the host machine, we must to initialize the project directory. We must to create new directory where we are going to clone the project 
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

~/testdb/test_db$ 

After that we can move the hole directory to the mapped volume /mysql-datadir/test_db/ in order to recreate the database inside the container.

- Making directory
#mkdir -p /mysql-datadir/test_db/

- Getting inside directory
#cd /mysql-datadir/test_db/

- Coping database dump to directory
#cp -R /mysql-datadir/test_db/ .

Finally we have required files for the starting of the database inside container:
Changelog                      employees.sql          load_dept_emp.dump      load_salaries1.dump  load_titles.dump  sakila            test_employees_md5.sql
employees_partitioned_5.1.sql  images                 load_dept_manager.dump  load_salaries2.dump  objects.sql       show_elapsed.sql  test_employees_sha.sql
employees_partitioned.sql      load_departments.dump  load_employees.dump     load_salaries3.dump  README.md         sql_test.sh

- Getting inside container.
# docker exec -it test-mysql bash

From inside the container, we must move to the working directory. Once there we can create employees database as follow:
mysql < employees.sql

We can either access or start a new CLI session from inside the container or from the host machine:
# docker exec -it test-mysql mysql -uroot -pmypassword

After this, we create a new use who we are going to use to call database using the PHP script.

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


Now that we are running a working mysql docker image container, we proceed to create the container which is going to become the test environment for the web application that is going to connect against the current mysql docker instance.

