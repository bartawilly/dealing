# Dealing

This task has been done based on codeigniter PHP framework. The task requirements were to read a CSV file contains clients and deals data as every client has an ID and Name and every deal has an ID and Name the file contain some extra columns date, accepted times and refused times. you can find this CSV file on CSV Data Folder. Aslo the task requires to initialize the needed database and tables. Also apply some filters, sorting and search on the output data.


## Installation

- Make sure you have you local apache server is up and running also have MySQL installed - you can use [Wampserver](http://www.wampserver.com/en/) or [XAMPP](https://www.apachefriends.org/index.html).
- Open bash and cd to your ./www directory of your apache local server.

- Clone the project
```bash
git clone https://github.com/bartawilly/dealing.git
```
Or instead you can go directly to [Dealing](https://github.com/bartawilly/dealing) and download it as ZIP and unzip it to ./wwww directory
IMPORTANT NOTE: if you going to use the github link to download the project as .ZIP file don't forget to rename the extracted folder to (dealing) instead of (dealing-master)

- Open your browser and write this URL (http://localhost/dealing/)

## Usage

- After the page loaded first you have to initialze the database and tables by click on initialze button and enter your local MySQL credentials and click initialize.

- Then you have to import the data from the CSV file by click on the import file then choose the file and click import.
- All the data will appear on the table and you can do some operations on it like search, filter or sort by using the table functions on the page.
- you can reset all - drop the database including all the tables and data - by using the red button at the end of the table. Once you click all will be reseted to the start point.

## Support

- If you need any help with the installation or usage you can directly send me an email to ahmednasser.bartawilly@gmail.com
