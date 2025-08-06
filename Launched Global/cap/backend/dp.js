import mysql from 'mysql2';
import dotenv from 'dotenv';

const db = mysql.createConnection({
  host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || 'Riksingha@615',
    database: process.env.DB_NAME || 'cab',  
    port: process.env.DB_PORT || 3306,
});

db.connect((err) => {
  if (err) {
    console.error('❌ MySQL connection failed:', err.message);
    process.exit(1);
  } else {
    console.log('✅ Connected to MySQL Database');
  }
});

db.query('SELECT * FROM users', (err, results) => {
  if (err) {
    console.error('❌ Error executing query:', err.message);
  } else {
    console.log('✅ Query results:', results);
  }
});

module.exports = db;
