// lib/db.ts

import mysql from 'mysql2/promise';

export async function connectToDatabase() {
  const connection = await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || 'Riksingha@615',
    database: process.env.DB_NAME || 'cab',
  });

  return connection;
}
