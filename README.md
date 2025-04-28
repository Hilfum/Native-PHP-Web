# Project Documentation

## Project Overview
This project is a web application designed to manage data related to tax documents. It allows users to input data through a form and view the stored records. The application interacts with a MySQL database to store and retrieve information.

## File Structure
- **input.php**: Handles the input form for data entry. Processes POST requests to insert data into the database and redirects to `show.php` upon successful insertion.
- **show.php**: Displays the data stored in the database, retrieving records from the `berkas` table and presenting them in a user-friendly format.
- **koneksi.php**: Establishes a connection to the database, containing the necessary credentials and connection logic to interact with the MySQL database.
- **style.css**: Contains the styles for the web application, defining the visual appearance of the HTML elements.
- **README.md**: Documentation for the project, explaining its purpose, setup instructions, and usage.
- **database/berkas.sql**: SQL script to create the `berkas` table in the database, defining the structure of the table, including the columns and their data types.

## Setup Instructions
1. **Database Setup**:
   - Create a MySQL database for the project.
   - Run the SQL script located in `database/berkas.sql` to create the necessary table.

2. **Configuration**:
   - Update the `koneksi.php` file with your database credentials (hostname, username, password, and database name).

3. **Running the Application**:
   - Place the project files in the web server's document root (e.g., `htdocs` for XAMPP).
   - Access `input.php` through your web browser to start using the application.

## Usage
- Use the form on `input.php` to enter data related to tax documents.
- After submitting the form, you will be redirected to `show.php`, where you can view the entered data.

## License
This project is open-source and available for modification and distribution under the MIT License.

tes tes tes